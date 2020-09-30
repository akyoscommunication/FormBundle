<?php

namespace Akyos\FormBundle\Controller;

use Akyos\CoreBundle\Repository\CoreOptionsRepository;
use Akyos\FormBundle\Entity\ContactFormField;
use Akyos\FormBundle\Form\ContactFormFieldType;
use Akyos\FormBundle\Form\NewContactFormFieldType;
use Akyos\FormBundle\Repository\ContactFormRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\HttpFoundation\File\File;


/**
 * @Route("/admin/contact/form/field", name="contact_form_field_")
 */
class ContactFormFieldController extends AbstractController
{
    protected $contactFormRepository;
    protected $request;
    protected $mailer;
    protected $coreOptionsRepository;
    protected $urlHelper;

    public function __construct(
        ContactFormRepository $contactFormRepository,
        RequestStack $request,
        \Swift_Mailer $mailer,
        CoreOptionsRepository $coreOptionsRepository,
        UrlHelper $urlHelper
    )
    {
        $this->contactFormRepository = $contactFormRepository;
        $this->request = $request;
        $this->mailer = $mailer;
        $this->coreOptionsRepository = $coreOptionsRepository;
        $this->urlHelper = $urlHelper;
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

    public function renderContactForm($idForm, $dynamicValues = [], $labels = true, $button_label = 'Envoyer', $object = null, $to = null, $formName = 'contactForm', $template = null): Response
    {
        $contactform = $this->contactFormRepository->find($idForm);

        $coreOptions = $this->coreOptionsRepository->findAll();
        if($coreOptions) {
            $coreOptions = $coreOptions[0];
        }

        $form_email = $this->get('form.factory')->createNamedBuilder($formName, ContactFormFieldType::class, null, array(
            'fields' => $contactform->getContactFormFields(),
            'labels' => $labels,
            'dynamicValues' => $dynamicValues,
            'site_key' => $coreOptions->getRecaptchaPublicKey()
        ))->getForm();

        $object = ( $object != null ? $object : $contactform->getFormObject() );
        $to = explode(',', ( $to != null ? $to : $contactform->getFormTo() ));
        $template = ( $template != null ? $template : ( $contactform->getTemplate() ? 'emails/'.$contactform->getTemplate().'.html.twig' : '@AkyosForm/templates/email/default.html.twig' ) );

        $form_email->handleRequest($this->request->getCurrentRequest());

        if ($form_email->isSubmitted() && $form_email->isValid()) {
            $result = $contactform->getMail();
            $files=[];
            $sendMail = true;

            /** @var ContactFormField $field */
            foreach ( $contactform->getContactFormFields() as $field ) {
                $data = $form_email->get($field->getSlug())->getData();

                if($field->getExcludeRegex()) {
                    $regex = '/'.$field->getExcludeRegex().'/';
                    if (preg_match($regex, $data)) {
                        $sendMail = false;
                    }
                }

                if($field->getType() == "file"){
                    if($data){
                        $files[] = $data;
                    }
                }
                if(is_array($data)) {
                    $data = implode(',', $data);
                }
                $result = str_replace('['.$field->getSlug().']', $data, $result);
                $object = str_replace('['.$field->getSlug().']', $data, $object);
            }

            $host = $this->request->getCurrentRequest()->getHost();
            $host = explode('.', $host);
            if ((count($host) > 2) && ($host[0] === 'www')) {
                $host = $host[1].'.'.$host[2];
            } else {
                $host = implode('.', $host);
            }

            $message = (new \Swift_Message($object))
                ->setFrom(['noreply@'.$host => ($coreOptions ? $coreOptions->getSiteTitle() : 'noreply')])
                ->setTo($to)
                ->setBody($this->renderView($template, [
                        'result' => $result,
                        'form' => $contactform
                    ]), 'text/html'
                );

            if(!empty($files)){
                foreach ($files as $file){
                    $message->attach(\Swift_Attachment::fromPath($file->getRealPath())->setFilename($file->getClientOriginalName()));
                }
            }
            if($sendMail) {
                try {
                    $this->mailer->send($message);
                    $this->addFlash('success', 'Votre message a bien été envoyé.');
                } catch (\Exception $e) {
                    $this->addFlash('warning', "Une erreur est survenue lors de l'envoi du message, veuillez réessayer plus tard.");
                }
            } else {
                $this->addFlash('danger', "Le formulaire n'est pas valide, veuillez vérifier votre saisie et réessayer.");
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
