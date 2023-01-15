<?php

namespace App\Repository;

use App\Entity\Act;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @method Act|null find($id, $lockMode = null, $lockVersion = null)
 * @method Act|null findOneBy(array $criteria, array $orderBy = null)
 * @method Act[]    findAll()
 * @method Act[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Act::class);
    }

    public function add(Act $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Act $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllActs()
    {
        return $this->createQueryBuilder('a')
            ->getQuery()->getArrayResult();
    }

    public function findActsByInitiator($user)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.initiator = :user')
            ->setParameter('user', $user)
            ->orderBy('a.requestDate', 'DESC')
            ->getQuery()->getArrayResult();
    }

    public function findCouncelActs($user)
    {
        return $this->createQueryBuilder('a')
            ->select('a.id,a.name,a.internalNumber,a.requestDate,a.folderName,a.signingDate,a.receptionDate,a.status,a.folderNumber,a.lastResentDate,au.actValidated,au.signedAt,au.signatureComment')
            ->leftJoin('a.actUser', 'au')
            ->leftJoin('au.user', 'u')
            ->andWhere('u.id = :user')
            ->andWhere("u.roles LIKE '%ROLE_COUNSEL%'")
            ->setParameter('user', $user)
            ->groupBy('au.id')
            ->getQuery()->getArrayResult();
    }

    public function findActById($id)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id = :id')
            ->setParameter('id', $id)
            ->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    public function findActByIdForUser($id, $user)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id = :id')
            ->setParameter('id', $id)
            ->andWhere('a.initiator = :user')
            ->setParameter('user', $user)
            ->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    public function findActCSV($user)
    {
        return $this->createQueryBuilder('a')
            ->select('a.id,a.name,a.internalNumber,DATE_FORMAT(a.requestDate,\'%d/%m/%Y\') as requestDate,a.folderName,DATE_FORMAT(a.signingDate,\'%d/%m/%Y\') as signingDate,DATE_FORMAT(a.receptionDate,\'%d/%m/%Y\') as receptionDate,a.status,a.folderNumber,DATE_FORMAT(a.lastResentDate,\'%d/%m/%Y\') as lastResentDate')
            ->andWhere('a.initiator = :user')
            ->setParameter('user', $user)
            ->getQuery()->getArrayResult();
    }

    public function countActInProgress($user)
    {
        return $this->createQueryBuilder('a')
            ->select('Count(a.id) as nbActInProgress')
            ->andWhere('a.initiator = :user')
            ->andWhere("a.status = 'En projet'")
            ->orWhere("a.status = 'Cree'")
            ->andWhere('a.initiator = :user')
            ->setParameter('user', $user)
            ->getQuery()->getOneOrNullResult();
    }

    public function countActSigning($user)
    {
        return $this->createQueryBuilder('a')
            ->select('Count(a.id) as nbActSigning')
            ->andWhere('a.initiator = :user')
            ->andWhere("a.status = 'En cours de signature'")
            ->orWhere("a.status = 'En cours de validation'")
            ->andWhere('a.initiator = :user')
            ->setParameter('user', $user)
            ->getQuery()->getOneOrNullResult();
    }

    public function countActSigned($user)
    {
        return $this->createQueryBuilder('a')
            ->select('Count(a.id) as nbActRefused')
            ->andWhere('a.initiator = :user')
            ->andWhere("a.status = 'Signee'")
            ->setParameter('user', $user)
            ->getQuery()->getOneOrNullResult();
    }

    public function countActSignedLastMonth($startDate, $endDate)
    {
        return $this->createQueryBuilder('a')
            ->select('Count(a.id) as nbActSigned')
            ->andWhere('a.signingDate > :startDate')
            ->andWhere('a.signingDate < :endDate')
            ->andWhere("a.status = 'Signee'")
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countActWaitingSigning()
    {
        return $this->createQueryBuilder('a')
            ->select('Count(a.id) as nbActWaitingSign')
            ->andWhere("a.status != 'Signee'")
            ->andWhere("a.status != 'abandonne'")
            ->andWhere("a.status != 'signature refuse'")
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countInitiator()
    {
        return $this->createQueryBuilder('a')
            ->select('Count(Distinct(a.initiator)) as nbInitiator')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getLastThreeSignedActs($user)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT a.folder_name , a.folder_number, c.last_name , c.name, au.signed_at, au.validated_at ,au.act_validated,au.signature_id, au.signature_comment
                FROM act_user au 
                Inner JOIN act a
                on a.id  = au.act_id 
                Inner JOIN `user` u 
                on u.id  = a.initiator_id  
                INNER JOIN contact c 
                on c.user_id = au.user_id 
                WHERE ( au.validated_at is NULL OR au.signed_at  is NOT NULL ) AND u.id = ".$user." 
                ORDER BY au.signed_at DESC
                limit 3;";
        try {
            $stmt = $conn->prepare($sql);
            $resultSet = $stmt->executeQuery();
            return $resultSet->fetchAllAssociative();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getLastThreeValidatedActs($user)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT a.folder_name , a.folder_number, c.last_name , c.name, au.signed_at, au.validated_at ,au.act_validated,au.signature_id, au.signature_comment 
                FROM act_user au 
                Inner JOIN act a
                on a.id  = au.act_id 
                Inner JOIN `user` u 
                on u.id  = a.initiator_id  
                INNER JOIN contact c 
                on c.user_id = au.user_id 
                WHERE (au.validated_at is NOT NULL OR au.signed_at  is NULL ) AND u.id = ".$user." 
                ORDER BY  au.validated_at  DESC
                limit 2;";
        try {
            $stmt = $conn->prepare($sql);
            $resultSet = $stmt->executeQuery();
            return $resultSet->fetchAllAssociative();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
