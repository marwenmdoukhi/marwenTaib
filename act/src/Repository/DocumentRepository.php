<?php

namespace App\Repository;

use App\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function findByDocumentByNameAndActId($name, $actId)
    {
        return $this->createQueryBuilder('d')
            ->select('d')
            ->andWhere('d.act = :id')
            ->setParameter('id', $actId)
            ->andWhere('d.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getSingleResult();

    }

    public function findDocumentsByAct($actId)
    {
        return $this->createQueryBuilder('d')
            ->select('d')
            ->andWhere('d.act = :id')
            ->setParameter('id', $actId)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

    }

    public function findAllDocuments()
    {
        return $this->createQueryBuilder('d')
            ->select('d.id,d.name,d.size,d.type,IDENTITY(d.act) as actId,d.position')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

    }

    public function findDocumentByActs($actArray,$user)
    {
        return $this->createQueryBuilder('d')
                    ->select('d.id,d.name,d.size,d.type,IDENTITY(d.act) as actId,d.position')
                    ->leftJoin('d.act' ,'a')
                    ->andWhere('IDENTITY(d.act) IN (:id)')
                    ->andWhere('a.initiator = :user')
                    ->setParameter('id' , $actArray)
                    ->setParameter('user' , $user)
                    ->getQuery()
                    ->getResult(Query::HYDRATE_ARRAY);
    }
    public function findDocumentForAct($actArray)
    {
        return $this->createQueryBuilder('d')
                    ->select('d.id,d.name,d.size,d.type,IDENTITY(d.act) as actId,d.position')
                    ->andWhere('IDENTITY(d.act) IN (:id)')
                    ->setParameter('id' , $actArray)
                    ->getQuery()
                    ->getResult(Query::HYDRATE_ARRAY);
    }

    public function findDocById($id)
    {
        return $this->createQueryBuilder('d')
            ->select('d.id,d.name,d.size,d.type,IDENTITY(d.act) as actId,d.position')
            ->andWhere('d.id =:id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

    }
    public function findDocByActIdCSV($act)
    {
        return $this->createQueryBuilder('d')
            ->select('d.id,d.name,d.size,d.type,a.name as actName,d.position')
            ->leftJoin('d.act','a')
            ->andWhere('d.act =:act')
            ->setParameter('act', $act)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

    }
}
