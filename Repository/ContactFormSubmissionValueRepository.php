<?php

namespace Akyos\FormBundle\Repository;

use Akyos\FormBundle\Entity\ContactFormSubmissionValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContactFormSubmissionValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactFormSubmissionValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactFormSubmissionValue[]    findAll()
 * @method ContactFormSubmissionValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactFormSubmissionValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactFormSubmissionValue::class);
    }

    // /**
    //  * @return ContactFormSubmissionValue[] Returns an array of ContactFormSubmissionValue objects
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
    public function findOneBySomeField($value): ?ContactFormSubmissionValue
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
