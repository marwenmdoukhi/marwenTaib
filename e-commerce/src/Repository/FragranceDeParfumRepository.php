<?php

namespace App\Repository;

use App\Entity\FragranceDeParfum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FragranceDeParfum|null find($id, $lockMode = null, $lockVersion = null)
 * @method FragranceDeParfum|null findOneBy(array $criteria, array $orderBy = null)
 * @method FragranceDeParfum[]    findAll()
 * @method FragranceDeParfum[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FragranceDeParfumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FragranceDeParfum::class);
    }

    // /**
    //  * @return FragranceDeParfum[] Returns an array of FragranceDeParfum objects
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
    public function findOneBySomeField($value): ?FragranceDeParfum
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
