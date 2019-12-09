<?php

namespace Akyos\FormBundle\Repository;

use Akyos\FormBundle\Entity\ContactFormField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ContactFormField|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactFormField|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactFormField[]    findAll()
 * @method ContactFormField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactFormFieldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactFormField::class);
    }

    // /**
    //  * @return FormField[] Returns an array of FormField objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FormField
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
