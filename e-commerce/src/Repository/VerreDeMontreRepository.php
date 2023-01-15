<?php

namespace App\Repository;

use App\Entity\VerreDeMontre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VerreDeMontre|null find($id, $lockMode = null, $lockVersion = null)
 * @method VerreDeMontre|null findOneBy(array $criteria, array $orderBy = null)
 * @method VerreDeMontre[]    findAll()
 * @method VerreDeMontre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerreDeMontreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VerreDeMontre::class);
    }

    // /**
    //  * @return VerreDeMontre[] Returns an array of VerreDeMontre objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VerreDeMontre
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
