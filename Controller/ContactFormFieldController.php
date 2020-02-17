<?php

namespace Akyos\FormBundle\Controller;

use Akyos\CoreBundle\Repository\CoreOptionsRepository;
use Akyos\FormBundle\Entity\ContactFormField;
use Akyos\FormBundle\Form\ContactFormFieldType;
use Akyos\FormBundle\Form\NewContactFormFieldType;
use Akyos\FormBundle\Repository\ContactFormRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/contact/form/field", name="contact_form_field_")
 */
class ContactFormFieldController extends AbstractController
{
    protected $contactFormRepository;
    protected $request;
    protected $mailer;
    protected $coreOptionsRepository;

    public function __construct(ContactFormRepository $contactFormRepository, RequestStack $request, \Swift_Mailer $mailer, CoreOptionsRepository $coreOptionsRepository)
    {
        $this->contactFormRepository = $contactFormRepository;
        $this->request = $request;
        $this->mailer = $mailer;
        $this->coreOptionsRepository = $coreOptionsRepository;
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param ContactFormField $contactFormField
     * @return Response
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
     * @param Request $request
     * @param ContactFormField $contactFormField
     * @return Response
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

    public function renderContactForm($idForm, $dynamicValues = [], $labels = true, $button_label = 'Envoyer'): Response
    {
        $contactform = $this->contactFormRepository->find($idForm);
        $form_email = $this->createForm(ContactFormFieldType::class, null, array(
            'fields' => $contactform->getContactFormFields(),
            'labels' => $labels,
            'dynamicValues' => $dynamicValues
        ));

        $form_email->handleRequest($this->request->getCurrentRequest());

        if ($form_email->isSubmitted() && $form_email->isValid()) {
            $result = $contactform->getMail();
            foreach ( $contactform->getContactFormFields() as $field ) {
                $data = $form_email->get($field->getSlug())->getData();
                if(is_array($data)) {
                    $data = implode(',', $data);
                }
                $result = str_replace('['.$field->getSlug().']', $data, $result);
            }

            $coreOptions = $this->coreOptionsRepository->findAll();
            if($coreOptions) {
                $coreOptions = $coreOptions[0];
            }

            $message = (new \Swift_Message($contactform->getFormObject()))
                ->setFrom(['noreply@'.$this->request->getCurrentRequest()->getHost() => ($coreOptions ? $coreOptions->getSiteTitle() : 'noreply')])
                ->setTo($contactform->getFormTo())
                ->setBody($this->renderView('@AkyosForm/templates/email.html.twig', [
                        'result' => $result,
                        'form' => $contactform
                    ]), 'text/html'
                );

            try {
                $this->mailer->send($message);
                $this->addFlash('success', 'Votre mail à bien été envoyé.');
            } catch (\Exception $e) {
                $this->addFlash('warning', "Une erreur est survenue lors de l'envoi du mail.");
            }
        }

        if($form_email->isSubmitted() && !$form_email->isValid()) {
            $this->addFlash('warning', "Le formulaire n'est pas valide, veuillez vérifier votre saisie et réessayer.");
        }

        return $this->render('@AkyosForm/templates/render.html.twig', [
            'button_label' => $button_label,
            'form_email' => $form_email->createView(),
        ]);
    }
}
