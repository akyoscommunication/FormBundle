<?php

namespace Akyos\FormBundle\Controller;

use Akyos\FormBundle\Entity\ContactForm;
use Akyos\FormBundle\Entity\ContactFormField;
use Akyos\FormBundle\Form\ContactFormType;
use Akyos\FormBundle\Form\NewContactFormFieldType;
use Akyos\FormBundle\Repository\ContactFormFieldRepository;
use Akyos\FormBundle\Repository\ContactFormRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/admin/contact-form", name="contact_form_")
 * @isGranted("formulaire-de-contact")
 */
class FormController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param ContactFormRepository $contactFormRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(ContactFormRepository $contactFormRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $contactFormRepository->createQueryBuilder('a');
        if($request->query->get('search')) {
            $query
                ->andWhere('a.title LIKE :keyword OR a.slug LIKE :keyword OR a.formTo LIKE :keyword')
                ->setParameter('keyword', '%'.$request->query->get('search').'%')
            ;
        }
        $els = $paginator->paginate($query->getQuery(), $request->query->getInt('page',1),12);

        return $this->render('@AkyosCore/crud/index.html.twig', [
            'els' => $els,
            'title' => 'Formulaire de contact',
            'entity' => 'Form',
            'route' => 'contact_form',
            'fields' => array(
                'ID' => 'Id',
                'Title' => 'Title',
                'Slug' => 'Slug',
                'Destinataire' => 'FormTo',
            ),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $contactForm = new ContactForm();
        $form = $this->createForm(ContactFormType::class, $contactForm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contactForm);
            $entityManager->flush();

            return $this->redirectToRoute('contact_form_index');
        }

        return $this->render('@AkyosForm/contact_form/new.html.twig', [
            'el' => $contactForm,
            'form' => $form->createView(),
            'title' => 'Formulaire de contact',
            'route' => 'contact_form',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param ContactForm $contactForm
     * @param ContactFormFieldRepository $contactFormFieldRepository
     * @return Response
     */
    public function edit(Request $request, ContactForm $contactForm, ContactFormFieldRepository $contactFormFieldRepository): Response
    {
        $contactFormField = new ContactFormField();
        $contactFormField->setContactForm($contactForm);
        $formContactFormField = $this->createForm(NewContactFormFieldType::class, $contactFormField);
        $form = $this->createForm(ContactFormType::class, $contactForm);
        $form->handleRequest($request);
        $formContactFormField->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('contact_form_index');
        }

        if ($formContactFormField->isSubmitted() && $formContactFormField->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $nbField = count($contactFormFieldRepository->findBy(array('contactForm' => $contactForm->getId())));
            $contactFormField->setPosition($nbField);
            $entityManager->persist($contactFormField);
            $entityManager->flush();

            return $this->redirectToRoute('contact_form_edit', ['id' => $contactForm->getId()]);
        }

        return $this->render('@AkyosForm/contact_form/edit.html.twig', [
            'el' => $contactForm,
            'title' => 'Formulaire de contact',
            'route' => 'contact_form',
            'entity' => 'ContactFormField',
            'form' => $form->createView(),
            'formContactFormField' => $formContactFormField->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param ContactForm $contactForm
     * @return Response
     */
    public function delete(Request $request, ContactForm $contactForm): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contactForm->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($contactForm);
            $entityManager->flush();
        }

        return $this->redirectToRoute('contact_form_index');
    }

    /**
     * @Route("/fields/change-position", methods={"POST"}, options={"expose"=true})
     * @param Request $request
     * @param ContactFormFieldRepository $contactFormFieldRepository
     * @return JsonResponse
     */
    public function changePosition(Request $request, ContactFormFieldRepository $contactFormFieldRepository): JsonResponse
    {
        foreach ($request->get('data') as $position => $field) {
            $field = $contactFormFieldRepository->find($field);
            $field->setPosition($position);
        }
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse('valid');
    }
}
