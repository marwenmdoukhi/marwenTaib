<?php

namespace App\Repository;

use App\Entity\SubSubCategories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SubSubCategories|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubSubCategories|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubSubCategories[]    findAll()
 * @method SubSubCategories[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubSubCategoriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubSubCategories::class);
    }

    /**
     * @param $value
     * @return int|mixed|string
     */
    public function findBySubSubCategories($value)
    {
        return $this->createQueryBuilder('ssc')
            ->join('ssc.subCategories', 'c')
            ->where('c.slug = :slug')
            ->setParameter('slug', $value)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $paramteres
     */
    public function findsub($paramteres)
    {
        $query = $this->getEntityManager()->createQuery('
        SELECT s.slug  as subslug ,ssb.name, ssb.slug,ssb.id,ssb.picture
                FROM App\Entity\Subcategory s 
                join s.subSubCategories ssb
                where s.slug = :ui
            ')->setParameter('ui', $paramteres)
            ->getResult()
            ;
        return $query;
    }
}
