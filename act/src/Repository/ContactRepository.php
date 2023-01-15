<?php

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }


    public function findContactForCSVById($id)
    {
        
        return $this->createQueryBuilder('c')
            ->select('c.id,c.email,c.phoneNumber,c.cnbId,c.name,c.lastName,c.enterpriseName,DATE_FORMAT(c.modificationDate,\'%d/%m/%Y\') as modificationDate,c.codeCountry')
            ->andWhere('c.initiator = :id')
            ->setParameter('id', $id)
            ->getQuery()->getArrayResult();
    }
    public function findALLuSERS()
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->leftJoin('c.contact', 'u')
            ->andWhere('u.cnbId IS NOT NULL')
            ->getQuery()->getArrayResult();
    }


}
