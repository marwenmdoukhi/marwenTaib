<?php

namespace App\Repository;

use App\Entity\MatieresDuLunette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MatieresDuLunette|null find($id, $lockMode = null, $lockVersion = null)
 * @method MatieresDuLunette|null findOneBy(array $criteria, array $orderBy = null)
 * @method MatieresDuLunette[]    findAll()
 * @method MatieresDuLunette[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatieresDuLunetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MatieresDuLunette::class);
    }

    // /**
    //  * @return MatieresDuLunette[] Returns an array of MatieresDuLunette objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MatieresDuLunette
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
