<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @param ManagerRegistry $registry
     * @param EntityManagerInterface $manager
     */
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Commande::class);
        $this->manager = $manager;
    }

    /**
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function benifice()
    {
        $query = $this->manager->createQuery('
            SELECT  SUM  (c.Montant) 
            FROM App\Entity\Commande c
            where 
            c.payer=1
        ');
        return  $query->getSingleScalarResult()  ;
    }

    /**
     * @return int|mixed|string
     */
    public function demandeaccepter()
    {
        $query = $this->manager->createQuery('
            SELECT c
            FROM App\Entity\Commande c 
            WHERE c.status= 1
            and 
            c.terminer= 0
        ');
        return  $query->getResult()  ;
    }

    /**
     * @param $user
     * @param $id
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function mescommandePanier($user, $id)
    {
        $query = $this->manager->createQuery(
            '
                SELECT c
                FROM App\Entity\Commande c , App\Entity\CommandeArticle ca
                where 
                c.users =:uc
                and 
                c.id=:id
                and 
                ca.Commande= c.id
               '
        );
        $query->SetParameters(array('uc' => $user,'id'=>$id));
        return $query->getOneOrNullResult();
    }

    /**
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function demande()
    {
        $query = $this->manager->createQuery('
                SELECT count(c)
                FROM App\Entity\Commande c 
                WHERE 
                c.terminer= 0
        ');
        return  $query->getSingleScalarResult()  ;
    }

    /**
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function Terminer()
    {
        $query = $this->manager->createQuery('
            SELECT count(c)
            FROM App\Entity\Commande c 
            WHERE
            c.terminer=1
        ');
        return  $query->getSingleScalarResult()  ;
    }

    /**
     * @return int|mixed|string
     */
    public function demandes()
    {
        $query = $this->manager->createQuery('
            SELECT c
            FROM App\Entity\Commande c 
            WHERE 
            c.terminer= 0
        ');
        return  $query->getResult()  ;
    }

    /**
     * @return int|mixed|string
     */
    public function demandeterminer()
    {
        $query =$this->manager->createQuery('
            SELECT c
            FROM App\Entity\Commande c 
            WHERE c.status= 1
            and 
            c.terminer= 1
        ');
        return  $query->getResult()  ;
    }

    /**
     * @return int|mixed|string
     */
    public function meilleurvente()
    {
        return $this->manager->createQuery('
                SELECT  SUM(c.Montant) as note, u.firstName, u.lastName,u.email,u.picture,u.tel
                FROM App\Entity\Commande c
                JOIN c.users a
                JOIN c.users u
                where 
                c.payer=true
                GROUP BY a
                ORDER BY note DESC 
            ')->setMaxResults(5)
            ->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function meilleurproduit()
    {
        return $this->manager->createQuery('
                SELECT  SUM(c.Product) as note, p.name, p.promo,p.newprice, p.filename
                FROM App\Entity\CommandeArticle c 
                JOIN c.Product a
                JOIN c.Product p
                JOIN  c.Commande ca
                where 
                ca.terminer=1
                GROUP BY a
                ORDER BY note DESC 
            ')->setMaxResults(6)
            ->getResult()
            ;
    }






    /**
     * @param $paramteres
     * @return int|mixed|string
     */
    public function detaildemande($paramteres)
    {
        $query = $this->manager->createQuery(
            "SELECT ca FROM App\Entity\Commande c ,App\Entity\CommandeArticle ca
            where 
            c.id=:ui
            and 
            c.id=ca.Commande
            "
        );
        $query->SetParameters(array('ui' => $paramteres));
        return $query->getResult();
    }

    /**
     * @param $user
     * @return int|mixed|string
     */
    public function mescommande($user)
    {
        return $this->createQueryBuilder('c')
            ->where('c.users = :uc')
            ->andWhere(' c.terminer=0')
            ->setParameter('uc', $user)
            ->addOrderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $user
     * @return int|mixed|string
     */
    public function historique($user)
    {
        return $this->createQueryBuilder('c')
            ->where('c.users = :uc')
            ->andWhere(' c.terminer=true')
            ->andWhere(' c.payer=true')
            ->setParameter('uc', $user)
            ->addOrderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $user
     * @param $id
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function order($user, $id)
    {
        return $this->createQueryBuilder('c')
            ->where('c.users = :uc')
            ->andWhere('c.Numero = :id')
            ->andWhere(' c.terminer=0')
            ->leftJoin('c.commandeArticles', 'ca')
            ->setParameters(array('uc'=> $user, 'id' => $id))
            ->addOrderBy('c.id', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function orderMail($user, $id)
    {
        return $this->createQueryBuilder('c')
            ->where('c.users = :uc')
            ->andWhere('c.Numero = :id')
            ->andWhere(' c.terminer=0')
            ->join('c.commandeArticles', 'ca')
            ->setParameters(array('uc'=> $user, 'id' => $id))
            ->addOrderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }
}
