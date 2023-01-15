<?php

namespace App\Repository;

use App\Entity\Cadre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cadre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cadre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cadre[]    findAll()
 * @method Cadre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CadreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cadre::class);
    }

    public function findByFormCadreSlug($paramteres)
    {
        $query =  $this->getEntityManager()->createQuery(
            "SELECT ca
                FROM App\Entity\Category c ,App\Entity\Cadre ca
                where ca.category = c.id        
                 and c.slug = :ui 
                "
        );
        $query->SetParameters(array('ui' => $paramteres ));
        return  $query->getResult();
    }

    /**
     * @param $paramteres
     * @return int|mixed|string
     */
    public function findBySubFormCadreSlug($paramteres)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT ca
                FROM App\Entity\Category c ,App\Entity\Cadre ca ,App\Entity\Subcategory sb
                where sb.category=c.id
                and ca.category = c.id        
                 and sb.slug = :ui 
                "
        );
        $query->SetParameters(array('ui' => $paramteres ));
        return  $query->getResult()  ;
    }

    /**
    //  * @return Cadre[] Returns an array of Cadre objects
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
    public function findOneBySomeField($value): ?Cadre
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
