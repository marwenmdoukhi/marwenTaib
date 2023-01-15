<?php

namespace App\Repository;

use App\Entity\Forme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Forme|null find($id, $lockMode = null, $lockVersion = null)
 * @method Forme|null findOneBy(array $criteria, array $orderBy = null)
 * @method Forme[]    findAll()
 * @method Forme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Forme::class);
    }

    /**
     * @param $paramteres
     * @return int|mixed|string
     */
    public function findByFormLunettSlug($paramteres)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT f
                FROM App\Entity\Category c ,App\Entity\Forme f 
                where f.category = c.id        
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
    public function findBySubFormLunetteSlug($paramteres)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT f
                FROM App\Entity\Category c ,App\Entity\Forme f ,App\Entity\Subcategory sb
                where sb.category=c.id
                and f.category = c.id        
                 and sb.slug = :ui 
                "
        );
        $query->SetParameters(array('ui' => $paramteres ));
        return  $query->getResult()  ;
    }
}
