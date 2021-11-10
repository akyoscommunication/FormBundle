<?php
namespace Akyos\FormBundle\Service;

use Akyos\CoreBundle\Entity\AdminAccess;
use Akyos\CoreBundle\Repository\AdminAccessRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ExtendAdminAccess
{
    private AdminAccessRepository $adminAccessRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(AdminAccessRepository $adminAccessRepository, EntityManagerInterface $entityManager)
    {
        $this->adminAccessRepository = $adminAccessRepository;
        $this->entityManager = $entityManager;
    }

    public function setDefaults(): Response
    {
        if (!$this->adminAccessRepository->findOneBy(['name' => 'Formulaire de contact']))
        {
            $adminAccess = new AdminAccess();
            $adminAccess
                ->setName("Formulaire de contact")
                ->setRoles([])
                ->setIsLocked(true)
            ;
            $this->entityManager->persist($adminAccess);
            $this->entityManager->flush();
        }

        if (!$this->adminAccessRepository->findOneBy(['name' => 'Formulaires envoyés']))
        {
            $adminAccess = new AdminAccess();
            $adminAccess
                ->setName("Formulaires envoyés")
                ->setRoles([])
                ->setIsLocked(true)
            ;
            $this->entityManager->persist($adminAccess);
            $this->entityManager->flush();
        }
        return new Response('true');
    }
}