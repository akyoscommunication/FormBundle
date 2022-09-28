<?php

namespace Akyos\FormBundle\Controller;

use Akyos\CoreBundle\Service\CoreMailer;
use Akyos\FormBundle\Entity\ContactForm;
use Akyos\FormBundle\Entity\ContactFormField;
use Akyos\FormBundle\Entity\ContactFormSubmission;
use Akyos\FormBundle\Entity\ContactFormSubmissionValue;
use Akyos\FormBundle\Form\ContactFormFieldType;
use Akyos\FormBundle\Form\NewContactFormFieldType;
use Akyos\FormBundle\Repository\ContactFormRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/contact/form/field', name: 'contact_form_field_')]
class ContactFormFieldController extends AbstractController
{
    public function __construct(
        private readonly ContactFormRepository $contactFormRepository,
        private readonly RequestStack $request,
        private readonly CoreMailer $mailer,
        private readonly EntityManagerInterface $entityManager,
        private readonly FormFactoryInterface $formFactory,
    ) {}

    /**
     * @param Request $request
     * @param ContactFormField $contactFormField
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route(path: '/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ContactFormField $contactFormField, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NewContactFormFieldType::class, $contactFormField);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return new Response('valid');
        }
        return $this->render('@AkyosForm/contact_form_field/edit.html.twig', ['el' => $contactFormField, 'route' => 'contact_form_field', 'form' => $form->createView(),]);
    }

    /**
     * @param Request $request
     * @param ContactFormField $contactFormField
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route(path: '/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request, ContactFormField $contactFormField, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contactFormField->getId(), $request->request->get('_token'))) {
            $entityManager->remove($contactFormField);
            $entityManager->flush();
        }
        /** @var ContactForm $contactForm */
        $contactForm = $contactFormField->getContactForm();
        return $this->redirectToRoute('contact_form_edit', ['id' => $contactForm->getId(),]);
    }

    public function renderContactForm($idForm, $dynamicValues = [], $labels = true, $button_label = 'Envoyer', $object = null, $to = null, $formName = 'contact_form', $template = null, $formTemplate = null): Response
    {
        /** @var ContactForm $contactform */
        $contactform = $this->contactFormRepository->find($idForm);

        $form_email = $this->formFactory->createNamedBuilder($formName, ContactFormFieldType::class, null, ['fields' => $contactform->getContactFormFields(), 'labels' => $labels, 'dynamicValues' => $dynamicValues,])->getForm();

        $object = ($object ?? $contactform->getFormObject());
        $to = explode(',', ($to ?? $contactform->getFormTo()));
        $template = ($template ?? ($contactform->getTemplate() ? 'emails/' . $contactform->getTemplate() . '.html.twig' : '@AkyosForm/templates/email/default.html.twig'));
        $form_email->handleRequest($this->request->getCurrentRequest());

        if ($form_email->isSubmitted() && $form_email->isValid()) {
            $result = $contactform->getMail();
            $files = [];
            $sendMail = true;
            $contactFormSubmission = new ContactFormSubmission();
            $contactFormSubmission->setContactForm($contactform);
            $contactFormSubmissionFiles = [];

            /** @var ContactFormField $field */
            foreach ($contactform->getContactFormFields() as $field) {
                $data = $form_email->get($field->getSlug())->getData();
                $contactFormSubmissionValue = new ContactFormSubmissionValue();
                $contactFormSubmissionValue->setContactFormSubmission($contactFormSubmission);
                $contactFormSubmissionValue->setContactFormField($field);

                if ($field->getExcludeRegex()) {
                    $regex = '/' . $field->getExcludeRegex() . '/';
                    if (preg_match($regex, $data)) {
                        $sendMail = false;
                    }
                }

                if ($data && $field->getType() === "file") {
                    $files[] = $data;
                    $originalFilename = pathinfo($data->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = str_replace(' ', '', trim(htmlspecialchars($originalFilename)));
                    $newFilename = $safeFilename . '-' . uniqid('', true) . '.' . $data->guessExtension();
                    try {
                        $data->move($this->getParameter('contact_form_files_directory'), $newFilename);
                        $contactFormSubmissionFiles[] = $this->getParameter('contact_form_files_directory') . $newFilename;
                        $contactFormSubmissionValue->setValue($this->getParameter('contact_form_files_directory') . $newFilename);
                    } catch (FileException $e) {
                        $sendMail = false;
                    }
                }

                if (is_array($data)) {
                    $data = implode(',', $data);
                }
                $result = str_replace('[' . $field->getSlug() . ']', $data, $result);
                $object = str_replace('[' . $field->getSlug() . ']', $data, $object);

                if ($field->getType() !== "file") {
                    $contactFormSubmissionValue->setValue($data);
                }
                $this->entityManager->persist($contactFormSubmissionValue);
            }

            /** @var Request $currentRequest */
            $currentRequest = $this->request->getCurrentRequest();
            $host = $currentRequest->getHost();
            $host = explode('.', $host);
            if ((count($host) > 2) && ($host[0] === 'www')) {
                $host = $host[1] . '.' . $host[2];
            } else {
                $host = implode('.', $host);
            }

            $body = $this->renderView($template, ['result' => $result, 'form' => $contactform]);

            $contactFormSubmission->setObject($object);
            $contactFormSubmission->setSentFrom('noreply@' . $host);
            $contactFormSubmission->setSentTo(implode(',', $to));
            $contactFormSubmission->setBody($body);
            $contactFormSubmission->setFiles($contactFormSubmissionFiles);

            $attachments = null;
            if (!empty($files)) {
                $attachments = [];
                foreach ($files as $file) {
                    $attachments[] = ['path' => $file->getRealPath(), 'name' => $file->getClientOriginalName(),];
                }
            }
            if ($sendMail) {
                try {
                    $this->mailer->sendMail($to, $object, '', $object, $template, null, null, null, null, ['templateParams' => ['result' => $result, 'form' => $contactform], 'attachments' => $attachments ?: null,]);
                    $this->entityManager->persist($contactFormSubmission);
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Votre message a bien été envoyé.');
                } catch (Exception $e) {
                    $this->addFlash('warning', "Une erreur est survenue lors de l'envoi du message, veuillez réessayer plus tard." . $e);
                }
            } else {
                $this->addFlash('danger', "Le formulaire n'est pas valide, veuillez vérifier votre saisie et réessayer.");
            }
        }

        if ($form_email->isSubmitted() && !$form_email->isValid()) {
            $this->addFlash('warning', "Le formulaire n'est pas valide, veuillez vérifier votre saisie et réessayer.");
        }

        $formTemplate = ($formTemplate ?? ($contactform->getFormTemplate() ? 'forms/' . $contactform->getFormTemplate() . '.html.twig' : '@AkyosForm/templates/render.html.twig'));

        return $this->render($formTemplate, ['button_label' => $button_label, 'form_email' => $form_email->createView(),]);
    }
}
