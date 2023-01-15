<?php

namespace App\Repository;

use App\Entity\Archive;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Archive|null find($id, $lockMode = null, $lockVersion = null)
 * @method Archive|null findOneBy(array $criteria, array $orderBy = null)
 * @method Archive[]    findAll()
 * @method Archive[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArchiveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Archive::class);
    }


    public function findArchiveByAct($actId)
    {
        return $this->createQueryBuilder('ar')
            ->select('ar.id,ar.action,DATE_FORMAT(ar.actionDate,\'%d/%m/%Y\') as actionD,ar.actor,a.internalNumber,a.name,a.folderNumber,a.folderName,ar.actionDate')
            ->leftJoin('ar.act','a')
            ->andWhere('ar.act = :id')
            ->setParameter('id', $actId)
            ->orderBy('ar.actionDate','DESC')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

    }

}
