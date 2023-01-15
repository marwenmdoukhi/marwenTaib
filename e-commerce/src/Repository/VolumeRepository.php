<?php

namespace App\Repository;

use App\Entity\Volume;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Volume|null find($id, $lockMode = null, $lockVersion = null)
 * @method Volume|null findOneBy(array $criteria, array $orderBy = null)
 * @method Volume[]    findAll()
 * @method Volume[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VolumeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Volume::class);
    }

    /**
     * @param $paramteres
     * @return int|mixed|string
     */
    public function findByVolumeSlug($paramteres)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT v
                FROM App\Entity\Category c ,App\Entity\Volume v 
                where v.category = c.id        
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
    public function findBySubVolumeSlug($paramteres)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT v
                FROM App\Entity\Category c ,App\Entity\Volume v  ,App\Entity\Subcategory sb
                where sb.category=c.id
                and v.category = c.id        
                 and sb.slug = :ui 
                "
        );
        $query->SetParameters(array('ui' => $paramteres ));
        return  $query->getResult()  ;
    }
}
