<?php

namespace App\Repository;

use App\Entity\Refusal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Refusal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Refusal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Refusal[]    findAll()
 * @method Refusal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RefusalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Refusal::class);
    }

    // /**
    //  * @return Refusal[] Returns an array of Refusal objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Refusal
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
