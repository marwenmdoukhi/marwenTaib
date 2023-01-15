<?php

namespace App\Repository;

use App\Entity\FonctionMontre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FonctionMontre|null find($id, $lockMode = null, $lockVersion = null)
 * @method FonctionMontre|null findOneBy(array $criteria, array $orderBy = null)
 * @method FonctionMontre[]    findAll()
 * @method FonctionMontre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FonctionMontreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FonctionMontre::class);
    }

    // /**
    //  * @return FonctionMontre[] Returns an array of FonctionMontre objects
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
    public function findOneBySomeField($value): ?FonctionMontre
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
