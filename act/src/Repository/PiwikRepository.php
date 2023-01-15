<?php

namespace App\Repository;

use App\Entity\Piwik;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Piwik|null find($id, $lockMode = null, $lockVersion = null)
 * @method Piwik|null findOneBy(array $criteria, array $orderBy = null)
 * @method Piwik[]    findAll()
 * @method Piwik[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PiwikRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Piwik::class);
    }

    // /**
    //  * @return Piwik[] Returns an array of Piwik objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Piwik
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
