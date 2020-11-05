<?php

namespace Akyos\FormBundle\Repository;

use Akyos\FormBundle\Entity\ContactFormSubmission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContactFormSubmission|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactFormSubmission|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactFormSubmission[]    findAll()
 * @method ContactFormSubmission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactFormSubmissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactFormSubmission::class);
    }

    // /**
    //  * @return ContactFormSubmission[] Returns an array of ContactFormSubmission objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ContactFormSubmission
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
