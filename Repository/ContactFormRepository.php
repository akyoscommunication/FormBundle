<?php

namespace Akyos\FormBundle\Repository;

use Akyos\FormBundle\Entity\ContactForm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContactForm|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactForm|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactForm[]    findAll()
 * @method ContactForm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactFormRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactForm::class);
    }

    // /**
    //  * @return Form[] Returns an array of Form objects
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
    public function findOneBySomeField($value): ?Form
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
