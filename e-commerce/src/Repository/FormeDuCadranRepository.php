<?php

namespace App\Repository;

use App\Entity\FormeDuCadran;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FormeDuCadran|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormeDuCadran|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormeDuCadran[]    findAll()
 * @method FormeDuCadran[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormeDuCadranRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormeDuCadran::class);
    }

    /**
     * @param $paramteres
     * @return int|mixed|string
     */
    public function findByFormdeHorlogrieSlug($paramteres)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT f
                FROM App\Entity\Category c ,App\Entity\FormeDuCadran f 
                where 
                f.category = c.id        
                and 
                c.slug = :ui 
                "
        );
        $query->SetParameters(array('ui' => $paramteres ));
        return  $query->getResult()  ;
    }

    public function findBySubFormHorlogrieSlug($paramteres)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT f
                FROM App\Entity\Category c ,App\Entity\FormeDuCadran f  ,App\Entity\Subcategory sb
                where sb.category=c.id
                and f.category = c.id        
                and sb.slug = :ui 
                "
        );
        $query->SetParameters(array('ui' => $paramteres ));
        return  $query->getResult()  ;
    }
}
