<?php

namespace Akyos\FormBundle\Controller;

use Akyos\FormBundle\Entity\ContactForm;
use Akyos\FormBundle\Entity\ContactFormField;
use Akyos\FormBundle\Form\ContactFormType;
use Akyos\FormBundle\Form\NewContactFormFieldType;
use Akyos\FormBundle\Repository\ContactFormFieldRepository;
use Akyos\FormBundle\Repository\ContactFormRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/contact-form', name: 'contact_form_')]
#[IsGranted('formulaire-de-contact')]
class FormController extends AbstractController
{
    /**
     * @param ContactFormRepository $contactFormRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(ContactFormRepository $contactFormRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $contactFormRepository->createQueryBuilder('a');
        if ($request->query->get('search')) {
            $query->andWhere('a.title LIKE :keyword OR a.slug LIKE :keyword OR a.formTo LIKE :keyword')->setParameter('keyword', '%' . $request->query->get('search') . '%');
        }
        $els = $paginator->paginate($query->getQuery(), $request->query->getInt('page', 1), 12);
        return $this->render('@AkyosCms/crud/index.html.twig', ['els' => $els, 'title' => 'Formulaire de contact', 'entity' => 'Form', 'route' => 'contact_form', 'fields' => ['ID' => 'Id', 'Title' => 'Title', 'Slug' => 'Slug', 'Destinataire' => 'FormTo',],]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contactForm = new ContactForm();
        $form = $this->createForm(ContactFormType::class, $contactForm);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contactForm);
            $entityManager->flush();

            return $this->redirectToRoute('contact_form_index');
        }
        return $this->render('@AkyosForm/contact_form/new.html.twig', ['el' => $contactForm, 'form' => $form->createView(), 'title' => 'Formulaire de contact', 'route' => 'contact_form',]);
    }

    /**
     * @param Request $request
     * @param ContactForm $contactForm
     * @param ContactFormFieldRepository $contactFormFieldRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route(path: '/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ContactForm $contactForm, ContactFormFieldRepository $contactFormFieldRepository, EntityManagerInterface $entityManager): Response
    {
        $contactFormField = new ContactFormField();
        $contactFormField->setContactForm($contactForm);
        $formContactFormField = $this->createForm(NewContactFormFieldType::class, $contactFormField);
        $form = $this->createForm(ContactFormType::class, $contactForm);
        $form->handleRequest($request);
        $formContactFormField->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('contact_form_index');
        }
        if ($formContactFormField->isSubmitted() && $formContactFormField->isValid()) {
            $nbField = count($contactFormFieldRepository->findBy(['contactForm' => $contactForm->getId()]));
            $contactFormField->setPosition($nbField);
            $entityManager->persist($contactFormField);
            $entityManager->flush();

            return $this->redirectToRoute('contact_form_edit', ['id' => $contactForm->getId()]);
        }
        return $this->render('@AkyosForm/contact_form/edit.html.twig', ['el' => $contactForm, 'title' => 'Formulaire de contact', 'route' => 'contact_form', 'entity' => 'ContactFormField', 'form' => $form->createView(), 'formContactFormField' => $formContactFormField->createView(),]);
    }

    /**
     * @param Request $request
     * @param ContactForm $contactForm
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route(path: '/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request, ContactForm $contactForm, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contactForm->getId(), $request->request->get('_token'))) {
            $entityManager->remove($contactForm);
            $entityManager->flush();
        }
        return $this->redirectToRoute('contact_form_index');
    }

    /**
     * @param Request $request
     * @param ContactFormFieldRepository $contactFormFieldRepository
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route(path: '/fields/change-position', methods: ['POST'], options: ['expose' => true])]
    public function changePosition(Request $request, ContactFormFieldRepository $contactFormFieldRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        foreach ($request->get('data') as $position => $field) {
            /** @var ContactFormField $field */
            $field = $contactFormFieldRepository->find($field);
            $field->setPosition($position);
        }
        $entityManager->flush();
        return new JsonResponse('valid');
    }
}
