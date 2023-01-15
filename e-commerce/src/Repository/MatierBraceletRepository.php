<?php

namespace App\Repository;

use App\Entity\MatierBracelet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MatierBracelet|null find($id, $lockMode = null, $lockVersion = null)
 * @method MatierBracelet|null findOneBy(array $criteria, array $orderBy = null)
 * @method MatierBracelet[]    findAll()
 * @method MatierBracelet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatierBraceletRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MatierBracelet::class);
    }

    /**
     * @param $paramteres
     * @return int|mixed|string
     */
    public function findByMatierBracletHorlogirieSlug($paramteres)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT m
             FROM App\Entity\Category c ,App\Entity\MatierBracelet m 
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
    public function findByMatierBracletSubHorlogrieSlug($paramteres)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT m
                FROM App\Entity\Category c ,App\Entity\MatierBracelet m  ,App\Entity\Subcategory sb
                where sb.category=c.id
                and m.category = c.id        
                 and sb.slug = :ui 
                "
        );
        $query->SetParameters(array('ui' => $paramteres ));
        return  $query->getResult()  ;
    }
}
