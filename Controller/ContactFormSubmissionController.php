<?php

namespace Akyos\FormBundle\Controller;

use Akyos\FormBundle\Entity\ContactFormSubmission;
use Akyos\FormBundle\Form\ContactFormSubmissionType;
use Akyos\FormBundle\Repository\ContactFormSubmissionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/contact-form-submission", name="contact_form_submission_")
 */
class ContactFormSubmissionController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param ContactFormSubmissionRepository $contactFormSubmissionRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(ContactFormSubmissionRepository $contactFormSubmissionRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $contactFormSubmissionRepository->createQueryBuilder('a');
        $query->innerJoin('a.contactForm', 'contactForm');
        if($request->query->get('search')) {
            $query
                ->andWhere('contactForm.title LIKE :keyword OR a.sentTo LIKE :keyword OR a.object LIKE :keyword')
                ->setParameter('keyword', '%'.$request->query->get('search').'%')
            ;
        }
        $els = $paginator->paginate($query->getQuery(), $request->query->getInt('page',1),12);

        return $this->render('@AkyosCore/crud/index.html.twig', [
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
}
