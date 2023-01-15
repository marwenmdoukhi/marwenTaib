<?php


namespace App\Repository;


use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findSignatoryByCreatedBy($user)
    {
         return $x = $this->getEntityManager()->createQueryBuilder()
            ->select("u.id,c.name,c.lastName,c.email,DATE_FORMAT(c.birthDate,'%d/%m/%Y') AS birthDate,c.phoneNumber,c.codeCountry,c.birthPlace,c.enterpriseName,c.siren,IDENTITY(au.act) as actId,au.validator,au.mailSent,DATE_FORMAT(au.validatedAt,'%d/%m/%Y') AS validatedAt,DATE_FORMAT(au.signedAt,'%d/%m/%Y') AS signedAt,au.comment,au.actValidated,au.signatureComment")
            ->from('App\Entity\Contact', 'c')
            ->leftJoin('c.contact', 'u')
            ->leftJoin('u.actUser', 'au')
            ->andwhere('c.initiator = :user')
            ->setParameter('user', $user)
             ->andWhere("u.roles LIKE '%ROLE_SIGNATORY%' OR u.roles LIKE '%ROLE_ENTERPRISE%'")
            ->groupBy('au.act,u.id')
            ->getQuery()->getArrayResult();
    }

    public function findCounselByCreatedBy($user)
    {
        return $x = $this->getEntityManager()->createQueryBuilder()
            ->select("u.id,c.name,c.lastName,c.email,DATE_FORMAT(c.birthDate,'%d/%m/%Y') AS birthDate,c.phoneNumber,c.codeCountry,c.codeCountry,c.birthPlace,c.enterpriseName,IDENTITY(au.act) as actId,au.validator,au.mailSent,DATE_FORMAT(au.validatedAt,'%d/%m/%Y') AS validatedAt,DATE_FORMAT(au.signedAt,'%d/%m/%Y') AS signedAt,au.comment,au.actValidated,au.signatureComment,u.emailApp AS emailApp,u.cnbId,u.email AS emailEd,u.phoneNumberApp,u.codeCountryApp,u.phoneNumber as phoneNumberEd,u.codeCountry as codeCountryEd,c.emailApp as counselEmail,c.phoneNumberApp as counselPhone,c.codeCountryApp as counselCodeCountryApp")
            ->from('App\Entity\Contact', 'c')
            ->leftJoin('c.contact', 'u')
            ->leftJoin('u.actUser', 'au')
            ->where('c.initiator = :user')
            ->setParameter('user', $user)
            ->andWhere('c.contact != :userc')
            ->setParameter('userc', $user)
            ->andWhere("u.roles NOT LIKE '%ROLE_SIGNATORY%'")
            ->andWhere("u.roles NOT LIKE '%ROLE_ENTERPRISE%'")
            ->groupBy('au.act,u.id')
            ->getQuery()->getArrayResult();
    }
    public function findCounselByCreatedByAuto($user,$name,$lastname)
    {
        return $x = $this->getEntityManager()->createQueryBuilder()
            ->select("u.id,c.name,c.lastName,c.email,DATE_FORMAT(c.birthDate,'%d/%m/%Y') AS birthDate,c.phoneNumber,c.codeCountry,c.codeCountry,c.birthPlace,c.enterpriseName,IDENTITY(au.act) as actId,au.validator,au.mailSent,DATE_FORMAT(au.validatedAt,'%d/%m/%Y') AS validatedAt,DATE_FORMAT(au.signedAt,'%d/%m/%Y') AS signedAt,au.comment,au.actValidated,au.signatureComment,u.emailApp AS emailApp,u.cnbId,u.email AS emailEd,u.phoneNumberApp,u.codeCountryApp,u.phoneNumber as phoneNumberEd,u.codeCountry as codeCountryEd,c.emailApp as counselEmail,c.phoneNumberApp as counselPhone,c.codeCountryApp as counselCodeCountryApp")
            ->from('App\Entity\Contact', 'c')
            ->leftJoin('c.contact', 'u')
            ->leftJoin('u.actUser', 'au')
            ->where('c.initiator = :user')
            ->setParameter('user', $user)
            ->andWhere('c.contact != :userc')
            ->setParameter('userc', $user)
            ->andWhere("u.name = :name")
            ->setParameter('name', $name)
            ->andWhere("u.lastName LIKE '%".$lastname."%'")
            ->andWhere("u.roles NOT LIKE '%ROLE_SIGNATORY%'")
            ->andWhere("u.roles NOT LIKE '%ROLE_ENTERPRISE%'")
            ->groupBy('au.act,u.id')
            ->getQuery()->setMaxResults(20)->getArrayResult();
    }


    public function findSignatoryByAct($act,$user)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select("u.id,c.name,c.lastName,c.email,DATE_FORMAT(c.birthDate,'%d/%m/%Y') AS birthDate,c.phoneNumber,c.codeCountry,c.codeCountry,c.birthPlace,c.enterpriseName,c.siren,IDENTITY(au.act) as actId,au.validator,au.mailSent,DATE_FORMAT(au.validatedAt,'%d/%m/%Y') AS validatedAt,DATE_FORMAT(au.signedAt,'%d/%m/%Y') AS signedAt,au.comment,au.actValidated,au.signatureComment,u.emailApp AS emailApp,u.cnbId,u.email AS emailEd,u.phoneNumberApp,u.codeCountryApp,u.phoneNumber as phoneNumberEd,u.codeCountry as codeCountryEd,u.ipaddress")
            ->from('App\Entity\Contact', 'c')
            ->leftJoin('c.contact', 'u')
            ->leftJoin('u.actUser', 'au')
            ->where('c.initiator = :user')
            ->setParameter('user', $user)
            ->andWhere("u.roles LIKE '%ROLE_SIGNATORY%' OR u.roles LIKE '%ROLE_ENTERPRISE%'")
            ->andwhere('au.act =:act ')
            ->setParameter('act', $act)
            ->orderBy('u.id')
            ->groupBy('c.contact')
            ->getQuery()->getArrayResult();
    }

    public function findCounselByAct($act)
    {
        return $this->createQueryBuilder('u')
            ->select('u.id,u.name,u.lastName,u.email,u.birthDate,u.phoneNumber,u.birthPlace,u.enterpriseName,u.siren')
            ->andwhere(':act MEMBER OF u.acts')
            ->setParameter('act', $act)
            ->andWhere("u.roles NOT LIKE '%ROLE_SIGNATORY%'")
            ->andWhere("u.roles NOT LIKE '%ROLE_ENTERPRISE%'")
            ->getQuery()->getArrayResult();
    }

    public function findUserById($id)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('u.id,c.name,c.lastName,c.email,c.birthDate,c.phoneNumber,c.codeCountry,c.birthPlace,c.enterpriseName,c.siren,u.roles,IDENTITY(au.act) as actId,au.validator,au.mailSent,au.validatedAt,au.signedAt,au.comment,au.actValidated,au.signatureComment')
            ->from('App\Entity\Contact', 'c')
            ->leftJoin('c.contact', 'u')
            ->leftJoin('u.actUser', 'au')
            ->andWhere('c.id =:id')
            ->setParameter('id', $id)
            ->groupBy('u.id')
            ->getQuery()->getOneOrNullResult();
    }

    public function findUserConnected($id)
    {
        return $this->createQueryBuilder('u')
            ->select('u.id,u.name,u.lastName,u.email,u.birthDate,u.phoneNumber,u.codeCountry,u.birthPlace,u.enterpriseName,u.siren,u.cnbId,u.emailApp,u.phoneNumberApp,DATE_FORMAT(u.resiliation,\'%d/%m/%Y\') AS resiliation,u.ipaddress,u.codeCountryApp')
            ->andWhere('u.id =:id')
            ->setParameter('id', $id)
            ->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }
    public function findUserConnectedOtp($id,$initiator)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('u.id,c.name,c.lastName,c.email,c.birthDate,c.phoneNumber,c.codeCountry,c.birthPlace,c.enterpriseName,c.siren,u.cnbId,u.emailApp,u.phoneNumberApp,DATE_FORMAT(u.resiliation,\'%d/%m/%Y\') AS resiliation')
            ->from('App\Entity\Contact', 'c')
            ->leftJoin('c.contact', 'u')
            ->andWhere('c.contact =:id')
            ->setParameter('id', $id)
            ->andWhere('c.initiator =:ini')
            ->setParameter('ini', $initiator)
            ->orderBy('c.modificationDate','DESC')
            ->getQuery()->setMaxResults(1)
            ->getResult(Query::HYDRATE_ARRAY);
    }

    public function findSignatoryForCounsel($user)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb1 = $this->getEntityManager()->createQueryBuilder()
            ->select('IDENTITY(a.initiator) as initiator')
            ->from('App\Entity\Act', 'a')
            ->leftJoin('a.actUser', 'au')
            ->leftJoin('au.user', 'u')
            ->andWhere('u.id = :user')
            ->andWhere("u.roles LIKE '%ROLE_COUNSEL%'")
            ->setParameter('user', $user)
            ->groupBy('au.id');
        $initiatorsIds = $qb1->getQuery()->getResult();
        $qb2 = $this->getEntityManager()->createQueryBuilder()
            ->select('IDENTITY(au.act) as act')
            ->from('App\Entity\Act', 'a')
            ->leftJoin('a.actUser', 'au')
            ->leftJoin('au.user', 'u')
            ->andWhere('u.id = :user')
            ->andWhere("u.roles LIKE '%ROLE_COUNSEL%'")
            ->setParameter('user', $user)
            ->groupBy('au.id');
        $actsIds = $qb2->getQuery()->getResult();

        return $this->getEntityManager()->createQueryBuilder()
            ->select("u.id,c.name,c.lastName,c.email,DATE_FORMAT(c.birthDate,'%d/%m/%Y') AS birthDate,c.phoneNumber,c.codeCountry,c.birthPlace,c.enterpriseName,c.siren,IDENTITY(au.act) as actId,au.validator,au.mailSent,DATE_FORMAT(au.validatedAt,'%d/%m/%Y') AS validatedAt,DATE_FORMAT(au.signedAt,'%d/%m/%Y') AS signedAt,au.comment,au.actValidated,au.signatureComment")
            ->from('App\Entity\Contact', 'c')
            ->leftJoin('c.contact', 'u')
            ->leftJoin('u.actUser', 'au')
            ->andWhere("u.roles LIKE '%ROLE_SIGNATORY%'")
            ->orWhere("u.roles LIKE '%ROLE_ENTERPRISE%'")
            ->andwhere($qb->expr()->in('c.initiator', !empty($initiatorsIds) ? array_column($initiatorsIds, 'initiator') : array(0)))
            ->andwhere($qb->expr()->in('au.act', !empty($actsIds) ? array_column($actsIds, 'act') : array(0)))
            ->groupBy('au.id')
            ->getQuery()->getArrayResult();
    }

    public function findCounselForCounsel($user)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb1 = $this->getEntityManager()->createQueryBuilder()
            ->select('IDENTITY(a.initiator) as initiator')
            ->from('App\Entity\Act', 'a')
            ->leftJoin('a.actUser', 'au')
            ->leftJoin('au.user', 'u')
            ->andWhere('u.id = :user')
            ->andWhere("u.roles NOT LIKE '%ROLE_SIGNATORY%'")
            ->andWhere("u.roles NOT LIKE '%ROLE_ENTERPRISE%'")
            ->setParameter('user', $user)
            ->groupBy('au.id');
        $initiatorsIds = $qb1->getQuery()->getResult();
        $qb2 = $this->getEntityManager()->createQueryBuilder()
            ->select('IDENTITY(au.act) as act')
            ->from('App\Entity\Act', 'a')
            ->leftJoin('a.actUser', 'au')
            ->leftJoin('au.user', 'u')
            ->andWhere('u.id = :user')
            ->andWhere("u.roles NOT LIKE '%ROLE_SIGNATORY%'")
            ->andWhere("u.roles NOT LIKE '%ROLE_ENTERPRISE%'")
            ->setParameter('user', $user)
            ->groupBy('au.id');
        $actsIds = $qb2->getQuery()->getResult();

        return $this->getEntityManager()->createQueryBuilder()
            ->select("u.id,c.name,c.lastName,c.email,DATE_FORMAT(c.birthDate,'%d/%m/%Y') AS birthDate,c.phoneNumber,c.codeCountry,c.birthPlace,c.enterpriseName,c.siren,IDENTITY(au.act) as actId,au.validator,au.mailSent,DATE_FORMAT(au.validatedAt,'%d/%m/%Y') AS validatedAt,DATE_FORMAT(au.signedAt,'%d/%m/%Y') AS signedAt,au.comment,au.actValidated,au.signatureComment,c.emailApp as counselEmail,c.phoneNumberApp as counselPhone,c.codeCountryApp as counselCodeCountryApp")
            ->from('App\Entity\Contact', 'c')
            ->leftJoin('c.contact', 'u')
            ->leftJoin('u.actUser', 'au')
            ->andWhere("u.roles NOT LIKE '%ROLE_SIGNATORY%'")
            ->andWhere("u.roles NOT LIKE '%ROLE_ENTERPRISE%'")
            ->andwhere($qb->expr()->in('c.initiator', !empty($initiatorsIds) ? array_column($initiatorsIds, 'initiator') : array(0)))
            ->andwhere($qb->expr()->in('au.act', !empty($actsIds) ? array_column($actsIds, 'act') : array(0)))
            ->groupBy('au.id')
            ->getQuery()->getArrayResult();
    }

    public function findContactSignatoryByUser($user)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('u.id,c.name,c.lastName,c.email,c.birthDate,c.phoneNumber,c.codeCountry,c.birthPlace,c.enterpriseName,c.siren,c.emailApp,c.phoneNumberApp,c.codeCountryApp,u.roles,c.cnbId,c.modificationDate')
            ->from('App\Entity\Contact', 'c')
            ->leftJoin('c.contact', 'u')
            ->andWhere("u.roles LIKE '%ROLE_SIGNATORY%'")
            ->orWhere("u.roles LIKE '%ROLE_ENTERPRISE%'")
            ->andwhere('c.initiator = :user')
            ->setParameter('user', $user)
            ->orderBy('c.modificationDate','DESC')
            ->groupBy('u.id')
            ->getQuery()->getArrayResult();
    }

    public function findContactCounselByUser($user)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('u.id,c.name,c.lastName,c.email,c.birthDate,c.phoneNumber,c.codeCountry,c.birthPlace,c.enterpriseName,c.siren,u.emailApp,c.codeCountryApp,u.roles,u.cnbId,c.modificationDate,u.email AS emailEd,u.phoneNumberApp,u.codeCountryApp,u.phoneNumber as phoneNumberEd,u.codeCountry as codeCountryEd,c.emailApp as counselEmail,c.phoneNumberApp as counselPhone,c.codeCountryApp as counselCodeCountryApp')
            ->from('App\Entity\Contact', 'c')
            ->leftJoin('c.contact', 'u')
            ->andWhere("u.roles NOT LIKE '%ROLE_SIGNATORY%'")
            ->andWhere("u.roles NOT LIKE '%ROLE_ENTERPRISE%'")
            ->andwhere('c.initiator = :user')
            ->setParameter('user', $user)
            ->orderBy('c.modificationDate','DESC')
            ->groupBy('u.id')
            ->getQuery()->getArrayResult();
    }

    public function findContactEdentitasByUser($user)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('u.id,u.name,u.lastName,u.email,u.birthDate,u.phoneNumber,u.codeCountry,u.birthPlace,u.enterpriseName,u.siren,u.emailApp,u.phoneNumberApp,u.codeCountryApp,u.roles,u.cnbId,c.modificationDate')
            ->from('App\Entity\User', 'u')
            ->leftJoin('u.contacts', 'c')
            ->andwhere('u.cnbId IS NOT NULL')
            //->andWhere("u.roles LIKE '%ROLE_USER%'")
            ->andWhere('u.id != :id')
            ->setParameter('id', $user)
            ->orderBy('c.modificationDate','DESC')
            ->groupBy('u.id')
            ->getQuery()->getArrayResult();
    }

    public function findEdentitasByCnbId($user)
    {
        return $x = $this->getEntityManager()->createQueryBuilder()
            ->select("u.id,u.name,u.lastName,u.email,DATE_FORMAT(u.birthDate,'%d/%m/%Y') AS birthDate,u.phoneNumber,u.codeCountry,u.codeCountry,u.birthPlace,u.enterpriseName,u.siren,IDENTITY(au.act) as actId,au.validator,au.mailSent,DATE_FORMAT(au.validatedAt,'%d/%m/%Y') AS validatedAt,DATE_FORMAT(au.signedAt,'%d/%m/%Y') AS signedAt,au.comment,au.actValidated,au.signatureComment,u.emailApp AS emailApp,u.cnbId,u.email AS emailEd,u.phoneNumberApp,u.codeCountryApp,u.phoneNumber as phoneNumberEd,u.codeCountry as codeCountryEd")
            ->from('App\Entity\User', 'u')
            ->leftJoin('u.contacts', 'c')
            ->leftJoin('u.actUser', 'au')
            ->andwhere('u.cnbId IS NOT NULL')
            ->andWhere('u.id != :userc')
            ->setParameter('userc', $user)
            ->andWhere("u.roles NOT LIKE '%ROLE_SIGNATORY%'")
            ->andWhere("u.roles NOT LIKE '%ROLE_ENTERPRISE%'")
            ->groupBy('au.act,u.id')
            ->getQuery()->getArrayResult();
    }
    public function findEdentitasByCnbIdAuto($user,$name,$lastname)
    {
        return $x = $this->getEntityManager()->createQueryBuilder()
            ->select("u.id,u.name,u.lastName,u.email,DATE_FORMAT(u.birthDate,'%d/%m/%Y') AS birthDate,u.phoneNumber,u.codeCountry,u.codeCountry,u.birthPlace,u.enterpriseName,u.siren,IDENTITY(au.act) as actId,au.validator,au.mailSent,DATE_FORMAT(au.validatedAt,'%d/%m/%Y') AS validatedAt,DATE_FORMAT(au.signedAt,'%d/%m/%Y') AS signedAt,au.comment,au.actValidated,au.signatureComment,u.emailApp AS emailApp,u.cnbId,u.email AS emailEd,u.phoneNumberApp,u.codeCountryApp,u.phoneNumber as phoneNumberEd,u.codeCountry as codeCountryEd,c.emailApp as counselEmail,c.phoneNumberApp as counselPhone,c.codeCountryApp as counselCodeCountryApp")
            ->from('App\Entity\User', 'u')
            ->leftJoin('u.contacts', 'c')
            ->leftJoin('u.actUser', 'au')
            ->andwhere('u.cnbId IS NOT NULL')
            ->andWhere('u.id != :userc')
            ->setParameter('userc', $user)
            ->andWhere("u.name = :name")
            ->setParameter('name', $name)
            ->andWhere("u.lastName LIKE '%".$lastname."%'")
            ->andWhere("u.roles NOT LIKE '%ROLE_SIGNATORY%'")
            ->andWhere("u.roles NOT LIKE '%ROLE_ENTERPRISE%'")
            ->groupBy('au.act,u.id')
            ->getQuery()->getArrayResult();
    }
    public function findUserByAct($act,$user)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select("u.id,c.name,c.lastName,c.email,DATE_FORMAT(c.birthDate,'%d/%m/%Y') AS birthDate,c.phoneNumber,c.codeCountry,c.codeCountry,c.birthPlace,c.enterpriseName,c.siren,IDENTITY(au.act) as actId,au.validator,au.mailSent,DATE_FORMAT(au.validatedAt,'%d/%m/%Y') AS validatedAt,DATE_FORMAT(au.signedAt,'%d/%m/%Y') AS signedAt,au.comment,au.actValidated,au.signatureComment,u.emailApp AS emailApp,u.cnbId,u.email AS emailEd,u.phoneNumberApp,u.codeCountryApp,u.phoneNumber as phoneNumberEd,u.codeCountry as codeCountryEd,u.ipaddress")
            ->from('App\Entity\Contact', 'c')
            ->leftJoin('c.contact', 'u')
            ->leftJoin('u.actUser', 'au')
            ->where('c.initiator = :user')
            ->setParameter('user', $user)
            ->andwhere('au.act =:act ')
            ->setParameter('act', $act)
            ->orderBy('u.id')
            ->getQuery()->getArrayResult();
    }

    public function findALLuSERS()
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from('App\Entity\User', 'u')
            ->where('u.cnbId IS NOT NULL')
            ->getQuery()->getArrayResult();
    }


}