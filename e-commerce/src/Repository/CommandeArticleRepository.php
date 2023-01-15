<?php

namespace App\Repository;

use App\Entity\CommandeArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommandeArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommandeArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommandeArticle[]    findAll()
 * @method CommandeArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommandeArticle::class);
    }

    /**
     * @param $id
     * @param $user
     * @return int|mixed|string
     */
    public function detailcommande($id, $user)
    {
        return $this->createQueryBuilder('ca')
            ->leftJoin('ca.Commande', 'c')
            ->where('c.users = :uc')
            ->andWhere('ca.Commande =:id')
            ->setParameters(array('uc'=> $user, 'id' => $id))
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $id
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function produit($id)
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT ca FROM App\Entity\Commande c , App\Entity\CommandeArticle ca
                where 
                ca.id=:id
                and 
                ca.Commande= c.id
               '
        );
        $query->SetParameters(array('id'=>$id));
        return $query->getOneOrNullResult();
    }

    /**
     * @param $user
     * @param $id
     * @return int|mixed|string
     */
    public function detailOrderMail($user, $id)
    {
        return $this->createQueryBuilder('ca')
            ->where('c.users = :uc')
            ->andWhere('c.Numero = :id')
            ->join('ca.Commande', 'c')
            ->setParameters(array('uc'=> $user, 'id' => $id))
            ->getQuery()
            ->getResult()
            ;
    }
}
