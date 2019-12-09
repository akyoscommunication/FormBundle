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

/**
 * @Route("/admin/contact-form", name="contact_form_")
 */
class FormController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(ContactFormRepository $contactFormRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $els = $paginator->paginate(
            $contactFormRepository->findAll(),
            $request->query->getInt('page', 1),
            12
        );

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
     */
    public function edit(Request $request, ContactForm $contactForm): Response
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
