<?php

namespace App\Repository;

use App\Entity\Style;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Style|null find($id, $lockMode = null, $lockVersion = null)
 * @method Style|null findOneBy(array $criteria, array $orderBy = null)
 * @method Style[]    findAll()
 * @method Style[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StyleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Style::class);
    }

    /**
     * @param $paramteres
     * @return int|mixed|string
     */
    public function findByStyleLunetteSlug($paramteres)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT s
             FROM App\Entity\Category c ,App\Entity\Style s 
             where s.category = c.id        
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
    public function findBySubStyleLunetteSlug($paramteres)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT s
                FROM App\Entity\Category c ,App\Entity\Style s ,App\Entity\Subcategory sb
                where sb.category=c.id
                and s.category = c.id        
                 and sb.slug = :ui 
            "
        );
        $query->SetParameters(array('ui' => $paramteres ));
        return  $query->getResult()  ;
    }
}
