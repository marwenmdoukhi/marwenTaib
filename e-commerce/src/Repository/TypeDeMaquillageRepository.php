<?php

namespace App\Repository;

use App\Entity\TypeDeMaquillage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeDeMaquillage|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeDeMaquillage|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeDeMaquillage[]    findAll()
 * @method TypeDeMaquillage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeDeMaquillageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeDeMaquillage::class);
    }

    /**
     * @param $paramteres
     * @return int|mixed|string
     */
    public function findByTypeDeMaquillageSlug($paramteres)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT t
                FROM App\Entity\Category c ,App\Entity\TypeDeMaquillage t 
                where t.category = c.id        
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
    public function findBySubTypeDeMaquillageSlug($paramteres)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT t
                FROM App\Entity\Category c ,App\Entity\TypeDeMaquillage t  ,App\Entity\Subcategory sb
                where sb.category=c.id
                and t.category = c.id        
                 and sb.slug = :ui 
                "
        );
        $query->SetParameters(array('ui' => $paramteres ));
        return  $query->getResult()  ;
    }
}
