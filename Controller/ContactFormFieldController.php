<?php

namespace Akyos\FormBundle\Controller;

use Akyos\FormBundle\Entity\ContactFormField;
use Akyos\FormBundle\Form\ContactFormFieldType;
use Akyos\FormBundle\Form\NewContactFormFieldType;
use Akyos\FormBundle\Repository\ContactFormRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/contact/form/field", name="contact_form_field_")
 */
class ContactFormFieldController extends AbstractController
{
    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ContactFormField $contactFormField): Response
    {
        $form = $this->createForm(NewContactFormFieldType::class, $contactFormField);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return new Response('valid');
        }

        return $this->render('@AkyosForm/contact_form_field/edit.html.twig', [
            'el' => $contactFormField,
            'route' => 'contact_form_field',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, ContactFormField $contactFormField): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contactFormField->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($contactFormField);
            $entityManager->flush();
        }

        return $this->redirectToRoute('contact_form_edit', [
            'id' => $contactFormField->getContactForm()->getId(),
        ]);
    }

    public function renderContactForm($idForm, ContactFormRepository $contactFormRepository, Request $request, \Swift_Mailer $mailer)
    {
        $contactform = $contactFormRepository->find($idForm);
        $form_email = $this->createForm(ContactFormFieldType::class, null, array('fields' => $contactform->getContactFormFields()));

        $form_email->handleRequest($request);

        if ($form_email->isSubmitted() && $form_email->isValid()) {
            $result = $contactform->getMail();
            foreach ( $contactform->getContactFormFields() as $field ) {
                $result = str_replace('['.$field->getSlug().']', $form_email->get($field->getSlug()), $contactform->getMail());
            }
            $message = (new \Swift_Mailer($contactform->getFormObject()))
                ->setFrom($form_email->get('email'))
                ->setTo($contactform->getFormTo())
                ->setBody(
                    $this->renderView(
                        '@AkyosForm/templates/email.html.twig',
                        [
                            'result' => $result,
                        ]
                    )
                );
        }

        return $this->render('@AkyosForm/templates/render.html.twig', [
            'form_email' => $form_email->createView(),
        ]);
    }
}
