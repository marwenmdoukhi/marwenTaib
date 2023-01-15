<?php

namespace App\Repository;

use App\Entity\TypeDuMouvement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeDuMouvement|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeDuMouvement|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeDuMouvement[]    findAll()
 * @method TypeDuMouvement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeDuMouvementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeDuMouvement::class);
    }


    /**
     * @param $paramteres
     * @return int|mixed|string
     */
    public function findByTypeDuMouvmentHorlogrieSlug($paramteres)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT t
                FROM App\Entity\Category c ,App\Entity\TypeDuMouvement t
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
    public function findByMouvmentHorlogrieSubHorlogrieSlug($paramteres)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT t
                FROM App\Entity\Category c ,App\Entity\TypeDuMouvement t  ,App\Entity\Subcategory sb
                where sb.category=c.id
                and t.category = c.id        
                 and sb.slug = :ui 
                "
        );
        $query->SetParameters(array('ui' => $paramteres ));
        return  $query->getResult()  ;
    }
}
