<?php

namespace Akyos\FormBundle\Controller;

use Akyos\FormBundle\Entity\ContactForm;
use Akyos\FormBundle\Entity\ContactFormSubmission;
use Akyos\FormBundle\Form\ContactFormSubmissionExportType;
use Akyos\FormBundle\Form\ContactFormSubmissionType;
use Akyos\FormBundle\Repository\ContactFormRepository;
use Akyos\FormBundle\Repository\ContactFormSubmissionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/admin/contact-form-submission", name="contact_form_submission_")
 * @isGranted("formulaires-envoyes")
 */
class ContactFormSubmissionController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param ContactFormSubmissionRepository $contactFormSubmissionRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(ContactFormSubmissionRepository $contactFormSubmissionRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $exportForm = $this->createForm(ContactFormSubmissionExportType::class);

        $exportForm->handleRequest($request);
        if($exportForm->isSubmitted() && $exportForm->isValid()) {
            /** @var ContactForm $contactForm */
            $contactForm = $exportForm->get('contactForm')->getData();
            return $this->generateCsv($contactForm, $contactFormSubmissionRepository);
        }

        $query = $contactFormSubmissionRepository->createQueryBuilder('a');
        $query->innerJoin('a.contactForm', 'contactForm');
        if($request->query->get('search')) {
            $query
                ->andWhere('contactForm.title LIKE :keyword OR a.sentTo LIKE :keyword OR a.object LIKE :keyword')
                ->setParameter('keyword', '%'.$request->query->get('search').'%')
            ;
        }
        $els = $paginator->paginate($query->getQuery(), $request->query->getInt('page',1),12);

        return $this->render('@AkyosForm/contact_form_submission/index.html.twig', [
            'els' => $els,
            'title' => 'Formulaire envoyés',
            'entity' => 'ContactFormSubmission',
            'route' => 'contact_form_submission',
            'button_add' => false,
            'search' => true,
            'fields' => array(
                'ID' => 'Id',
                'Formulaire' => 'contactForm',
                'Destinataire' => 'sentTo',
                'Objet' => 'object',
            ),
            'exportForm' => $exportForm->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param ContactFormSubmission $contactFormSubmission
     * @return Response
     */
    public function edit(Request $request, ContactFormSubmission $contactFormSubmission): Response
    {
        $form = $this->createForm(ContactFormSubmissionType::class, $contactFormSubmission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('contact_form_submission_index');
        }

        return $this->render('@AkyosForm/contact_form_submission/edit.html.twig', [
            'el' => $contactFormSubmission,
            'title' => 'Formulaire envoyé',
            'route' => 'contact_form_submission',
            'entity' => 'ContactFormSubmission',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param ContactFormSubmission $contactFormSubmission
     * @return Response
     */
    public function delete(Request $request, ContactFormSubmission $contactFormSubmission): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contactFormSubmission->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($contactFormSubmission);
            $entityManager->flush();
        }

        return $this->redirectToRoute('contact_form_submission_index');
    }

    public function generateCsv(ContactForm $contactForm, ContactFormSubmissionRepository $contactFormSubmissionRepository) {
        $contactFormSubmissions = $contactFormSubmissionRepository->findBy(['contactForm' => $contactForm]);
        $response = new StreamedResponse();
        $csvColumns = [
            'ID',
            'Formulaire',
            'Destinataire',
            'Expéditeur',
            'Objet',
            'Date d\'envoi',
        ];
        foreach ($contactForm->getContactFormFields() as $contactFormField) {
            $csvColumns[] = $contactFormField->getTitle();
        }
        $response->setCallback(function() use($contactFormSubmissions, $csvColumns) {
            $handle = fopen('php://output', 'wb+');
            fputcsv($handle, $csvColumns, ';');

            foreach ($contactFormSubmissions as $contactFormSubmission) {
                /** @var ContactFormSubmission $contactFormSubmission */

                $lineContent = [
                    $contactFormSubmission->getId(),
                    $contactFormSubmission->getContactForm()->getTitle(),
                    $contactFormSubmission->getSentTo(),
                    $contactFormSubmission->getSentFrom(),
                    $contactFormSubmission->getObject(),
                    date_format($contactFormSubmission->getCreatedAt(), 'd/m/Y'),
                ];
                foreach ($csvColumns as $column) {
                    foreach ($contactFormSubmission->getContactFormSubmissionValues() as $value) {
                        if($value->getContactFormField()->getTitle() === $column) {
                            $lineContent[] = $value->getValue();
                        }
                    }
                }

                fputcsv($handle,$lineContent,';');
            }

            fclose($handle);
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/force-download; charset=utf-8');
        $response->headers->set('Content-Disposition','attachment; filename=Formulaire_'.$contactForm->getSlug().'.csv');

        return $response;
    }
}
