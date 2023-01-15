<?php

namespace App\Repository;

use App\Entity\PlaquettesDeNez;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlaquettesDeNez|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaquettesDeNez|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaquettesDeNez[]    findAll()
 * @method PlaquettesDeNez[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaquettesDeNezRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaquettesDeNez::class);
    }

    // /**
    //  * @return PlaquettesDeNez[] Returns an array of PlaquettesDeNez objects
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
    public function findOneBySomeField($value): ?PlaquettesDeNez
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
