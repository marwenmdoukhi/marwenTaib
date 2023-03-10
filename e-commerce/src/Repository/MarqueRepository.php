<?php

namespace App\Repository;

use App\Entity\Marque;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Marque|null find($id, $lockMode = null, $lockVersion = null)
 * @method Marque|null findOneBy(array $criteria, array $orderBy = null)
 * @method Marque[]    findAll()
 * @method Marque[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Marque::class);
    }

    /**
     * @param $paramteres
     * @return int|mixed|string
     */
    public function findByMarqueSlug($paramteres)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT m
             FROM App\Entity\Category c ,App\Entity\Marque m 
             where m.category = c.id        
             and c.slug = :ui 
             "
        );
        $query->SetParameters(array('ui' => $paramteres ));
        return  $query->getResult()  ;
    }

    /**
     * @param $paramteres
     * @return int|mixed|string
     */
    public function findBySubMarqueSlug($paramteres)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT m
            FROM App\Entity\Category c ,App\Entity\Marque m ,App\Entity\Subcategory sb
            where sb.category=c.id
            and m.category = c.id        
             and sb.slug = :ui 
            "
        );
        $query->SetParameters(array('ui' => $paramteres ));
        return  $query->getResult()  ;
    }
}
