<?php

namespace App\Repository;

use App\Entity\CguUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CguUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method CguUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method CguUser[]    findAll()
 * @method CguUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CguUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CguUser::class);
    }

    // /**
    //  * @return CguUser[] Returns an array of CguUser objects
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
    public function findOneBySomeField($value): ?CguUser
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
