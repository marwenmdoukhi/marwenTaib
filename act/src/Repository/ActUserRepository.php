<?php

namespace App\Repository;

use App\Entity\ActUser;
use App\Entity\Contact;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ActUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActUser[]    findAll()
 * @method ActUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActUser::class);
    }


    public function findSignatoryWithValidated($act)
    {
        return $this->createQueryBuilder('au')
            ->select('u.id,u.email')
            ->leftJoin('au.user','u')
            ->andWhere('au.actValidated = true')
            ->andWhere('au.validator = true')
            ->andWhere('au.act =:act')
            ->andWhere("u.roles LIKE '%ROLE_SIGNATORY%' OR u.roles LIKE '%ROLE_ENTERPRISE%'")
            ->setParameter('act',$act)
            ->groupBy('u.id')
            ->getQuery()->getArrayResult();
    }

    public function countActValidatedLastMonth($startDate, $endDate)
    {
        return $this->createQueryBuilder('au')
            ->select('Count(Distinct(au.act)) as nbActValidated')
            ->andWhere('au.validatedAt > :startDate')
            ->andWhere('au.validatedAt < :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countSignaturesCurrentYear($date)
    {
        return $this->createQueryBuilder('au')
            ->select('Count(au.act) as nbSignature')
            ->andWhere('au.signedAt is Not Null')
            ->andWhere('au.signedAt > :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function findSignatoryWithNotSigned($act)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from(User::class,'u')
            ->leftJoin('u.actUser','au')
            ->andWhere('au.act=:act')
            ->andWhere('au.signedAt is Null')
            ->andWhere("u.roles LIKE '%ROLE_SIGNATORY%' OR u.roles LIKE '%ROLE_ENTERPRISE%'")
            ->setParameter('act',$act)
            ->groupBy('au.id')
            ->getQuery()->getArrayResult();
    }
    public function findSignatoryWithSigned($act)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('c.name,c.lastName,c.email,c.phoneNumber,IDENTITY(au.act) as act')
            ->from(Contact::class,'c')
            ->leftJoin('c.contact','u')
            ->leftJoin('u.actUser','au')
            ->andWhere('au.act=:act')
            ->andWhere('au.signedAt is Not Null')
            ->andWhere("u.roles LIKE '%ROLE_SIGNATORY%' OR u.roles LIKE '%ROLE_ENTERPRISE%'")
            ->setParameter('act',$act)
            ->groupBy('au.id')
            ->getQuery()->getArrayResult();
    }
    public function findSignatory($act)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from(User::class,'u')
            ->leftJoin('u.actUser','au')
            ->andWhere('au.act=:act')
            ->andWhere("u.roles LIKE '%ROLE_SIGNATORY%' OR u.roles LIKE '%ROLE_ENTERPRISE%'")
            ->setParameter('act',$act)
            ->groupBy('u.id')
            ->getQuery()->getArrayResult();
    }

    public function findUsers($act)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from(User::class ,'u')
            ->leftJoin('u.actUser','au')
            ->andWhere('au.act=:act')
            ->setParameter('act',$act)
            ->groupBy('au.id')
            ->getQuery()->getArrayResult();
    }


    public function findActUsersCSV($act,$user)
    {
        return $this->createQueryBuilder('au')
            ->select('au.id,c.name as contactName,c.lastName as contactLastName,a.name as actName,au.validator,au.actValidated,au.mailSent,au.comment,DATE_FORMAT(au.validatedAt,\'%d/%m/%Y\') as validatedAt,DATE_FORMAT(au.signedAt,\'%d/%m/%Y\') as signedAt,au.signatureComment')
            ->leftJoin('au.act','a')
            ->leftJoin('au.user','u')
            ->leftJoin('u.contacts','c')
            ->andWhere('au.act=:act')
            ->andWhere('c.initiator=:user')
            ->setParameter('act',$act)
            ->setParameter('user',$user)
            ->getQuery()->getArrayResult();
    }

    public function findActUsers($user)
    {
        return $this->createQueryBuilder('au')
        ->select('au.id,c.name as contactName,a.status as status,a.id as actId , IDENTITY(au.user) as user')
        ->leftJoin('au.act','a')
        ->leftJoin('au.user','u')
        ->leftJoin('u.contacts','c')
        ->andWhere('a.initiator=:user')
        ->setParameter('user',$user)
        ->getQuery()->getArrayResult();
    }
}
