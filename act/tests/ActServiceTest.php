<?php


namespace App\Tests;


use App\Entity\Act;
use App\Entity\ActUser;
use App\Entity\Archive;
use App\Entity\Document;
use App\Entity\User;
use App\Service\ActService;
use App\Service\DocumentService;
use App\Service\MailService;
use App\Service\OrderService;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ActServiceTest extends TestCase
{
    public function testGetActs()
    {
        $actMock = new Act();
        $actMock->setName('test');
        $actMock->setStatus('En projet');
        $actMock->setFolderName('test');
        $actMock->setFolderNumber('123456');
        $serializer = new Serializer(array(new ObjectNormalizer()));
        $data = $serializer->normalize($actMock,null);
        $data["requestDate"]=new \DateTime();
        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('findActsByInitiator'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('findActsByInitiator')
            ->willReturn([$data]);
        $objectManager = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $objectManager->expects($this->any())
            ->method('getRepository')
            ->withAnyParameters()
            ->willReturn($repositoryMock);
        $kernel = $this
            ->getMockBuilder(KernelInterface::class)
            ->setMethods(array('getProjectDir'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $documentService = $this->getMockBuilder(DocumentService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailService = $this->getMockBuilder(MailService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $orderService = $this->getMockBuilder(OrderService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $actService = new ActService($objectManager, $orderService, $mailService, $documentService, $kernel);
        $res = $actService->getActs(1);
        $this->assertNotEmpty($res);
    }

    public function testActArchive()
    {
        $archiveMock = new Archive();

        $archiveMock->setAction('signature');
        $archiveMock->setActor('fares tayari');
        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('findArchiveByAct'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('findArchiveByAct')
            ->willReturn($archiveMock);
        $objectManager = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $objectManager->expects($this->any())
            ->method('getRepository')
            ->withAnyParameters()
            ->willReturn($repositoryMock);
        $kernel = $this
            ->getMockBuilder(KernelInterface::class)
            ->setMethods(array('getProjectDir'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $documentService = $this->getMockBuilder(DocumentService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailService = $this->getMockBuilder(MailService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $orderService = $this->getMockBuilder(OrderService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $actService = new ActService($objectManager, $orderService, $mailService, $documentService, $kernel);
        $result = $actService->actArchive(1);
//        dump($result);die;
        $this->assertNotEmpty($result);
    }

    public function testNewAct()
    {
        $actMock = new Act();
        $userMock = new User();
        $userMock->setName('fares');
        $actMock->setStatus('En Projet');
        $actMock->setInitiator($userMock);
        $serializer = new Serializer(array(new ObjectNormalizer()));
        $data = $serializer->normalize($actMock,null);
        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('findActById'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('findActById')
            ->willReturn([$data]);
        $objectManager = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $objectManager->expects($this->any())
            ->method('getRepository')
            ->withAnyParameters()
            ->willReturn($repositoryMock);
        $kernel = $this
            ->getMockBuilder(KernelInterface::class)
            ->setMethods(array('getProjectDir'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $documentService = $this->getMockBuilder(DocumentService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailService = $this->getMockBuilder(MailService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $orderService = $this->getMockBuilder(OrderService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $actService = new ActService($objectManager, $orderService, $mailService, $documentService, $kernel);
        $result = $actService->newAct($data,$userMock);
        $this->assertNotEmpty($result);
    }

    public function testGetCouncelActs()
    {
        $actMock = new Act();
        $actMock->setName('test');
        $actMock->setStatus('En projet');
        $actMock->setFolderName('test');
        $actMock->setFolderNumber('123456');
        $serializer = new Serializer(array(new ObjectNormalizer()));
        $data = $serializer->normalize($actMock,null);
        $data["requestDate"]=new \DateTime();
        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('findCouncelActs'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('findCouncelActs')
            ->willReturn([$data]);
        $objectManager = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $objectManager->expects($this->any())
            ->method('getRepository')
            ->withAnyParameters()
            ->willReturn($repositoryMock);
        $kernel = $this
            ->getMockBuilder(KernelInterface::class)
            ->setMethods(array('getProjectDir'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $documentService = $this->getMockBuilder(DocumentService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailService = $this->getMockBuilder(MailService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $orderService = $this->getMockBuilder(OrderService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $actService = new ActService($objectManager, $orderService, $mailService, $documentService, $kernel);
        $res = $actService->getCouncelActs(1);
        $this->assertNotEmpty($res);
    }

    public function testGetActById()
    {
        $actMock = new Act();
        $actMock->setName('test');
        $actMock->setStatus('En projet');
        $actMock->setFolderName('test');
        $actMock->setFolderNumber('123456');
        $actMock->setRequestDate(new \DateTime());
        $serializer = new Serializer(array(new ObjectNormalizer()));
        $data = $serializer->normalize($actMock,null);
        $data["requestDate"]=new \DateTime();
//        dump($data);die;
        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('findActById'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('findActById')
            ->willReturn($data);
        $objectManager = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $objectManager->expects($this->any())
            ->method('getRepository')
            ->withAnyParameters()
            ->willReturn($repositoryMock);
        $kernel = $this
            ->getMockBuilder(KernelInterface::class)
            ->setMethods(array('getProjectDir'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $documentService = $this->getMockBuilder(DocumentService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailService = $this->getMockBuilder(MailService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $orderService = $this->getMockBuilder(OrderService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $actService = new ActService($objectManager, $orderService, $mailService, $documentService, $kernel);
        $res = $actService->getActById(2);
        $this->assertNotEmpty($res);
    }

    public function testUpdateAct()
    {
        $date = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);
        $actMock = new Act();
        $userMock = new User();

        $actMock->setName('test');
        $actMock->setStatus('En Projet');
        $actMock->setFolderName('test');
        $actMock->setFolderNumber('123456');
        $serializer = new Serializer(array(new ObjectNormalizer()));
        $data = $serializer->normalize($actMock,null);
        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('find','findActById'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('find')
            ->willReturn($actMock);
        $repositoryMock->expects($this->any())
            ->method('findActById')
            ->willReturn($data);
        $objectManager = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $objectManager->expects($this->any())
            ->method('getRepository')
            ->withAnyParameters()
            ->willReturn($repositoryMock);
        $kernel = $this
            ->getMockBuilder(KernelInterface::class)
            ->setMethods(array('getProjectDir'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $documentService = $this->getMockBuilder(DocumentService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailService = $this->getMockBuilder(MailService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $orderService = $this->getMockBuilder(OrderService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $actService = new ActService($objectManager, $orderService, $mailService, $documentService, $kernel);
        $res = $actService->updateAct($data,$userMock);
        $this->assertNotEmpty($res);
    }

    public function testSetValidator()
    {
        $userMock = new User();
        $actUserMock = new ActUser();
        $returnUserMock=$this->getMockBuilder(User::class)
            ->setMethods(array('getId'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $returnUserMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $returnActMock = $this->getMockBuilder(User::class)
            ->setMethods(array('getAct'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $returnActMock->expects($this->any())
            ->method('getAct')
            ->will($this->returnValue(1));
        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('find','findOneBy'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('find')
            ->withAnyParameters()
            ->willReturn($returnUserMock);
        $repositoryMock->expects($this->any())
            ->method('find')
            ->willReturn($returnActMock);
        $repositoryMock->expects($this->any())
            ->method('findOneBy')
            ->willReturn($actUserMock);
        $objectManager = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $objectManager->expects($this->any())
            ->method('getRepository')
            ->withAnyParameters()
            ->willReturn($repositoryMock);
        $kernel = $this
            ->getMockBuilder(KernelInterface::class)
            ->setMethods(array('getProjectDir'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $documentService = $this->getMockBuilder(DocumentService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailService = $this->getMockBuilder(MailService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $orderService = $this->getMockBuilder(OrderService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $actService = new ActService($objectManager, $orderService, $mailService, $documentService, $kernel);
        $res = $actService->setValidator(['id'=>1,'actId'=>1]);
        $this->assertNotEmpty($res);
    }

    public function testDeleteAct()
    {
        $actUserMock = new ActUser();
        $documentMock = new Document();
        $returnActMock=$this->getMockBuilder(Act::class)
            ->setMethods(array('getId'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $returnActMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('find','findBy'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('find')
            ->willReturn($returnActMock);
        $repositoryMock->expects($this->any())
            ->method('findBy')
            ->willReturn($actUserMock);
        $repositoryMock->expects($this->any())
            ->method('findBy')
            ->willReturn($documentMock);
        $objectManager = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $objectManager->expects($this->any())
            ->method('getRepository')
            ->withAnyParameters()
            ->willReturn($repositoryMock);
        $kernel = $this
            ->getMockBuilder(KernelInterface::class)
            ->setMethods(array('getProjectDir'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $documentService = $this->getMockBuilder(DocumentService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailService = $this->getMockBuilder(MailService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $orderService = $this->getMockBuilder(OrderService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $actService = new ActService($objectManager, $orderService, $mailService, $documentService, $kernel);
        $res = $actService->deleteAct(['id'=>1]);
        $this->assertNotEmpty($res);
    }

    public function testRefuseAct()
    {
        $date = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);

        $actUserMock = new ActUser();
        $userMock = new User();
        $userMock->setLastName('tayari');
        $userMock->setName('fares');
        $archiveMock = new Archive();
        $actMock = new Act();
        $actMock->setName('test');
        $actMock->setStatus('En projet');
        $actMock->setFolderName('test');
        $actMock->setFolderNumber('123456');
        $actMock->setRequestDate(new \DateTime());
        $returnActUserMock=$this->getMockBuilder(ActUser::class)
            ->setMethods(array('getUser'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $returnActUserMock->expects($this->any())
            ->method('getUser')
            ->willReturn($userMock);


        $returnUserMock=$this->getMockBuilder(User::class)
            ->setMethods(array('getId'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $returnUserMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('find','findOneBy','findActById' ,'findBy'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('find')
            ->willReturn($actMock);
        $repositoryMock->expects($this->any())
            ->method('findOneBy')
            ->willReturn($returnActUserMock);
        $repositoryMock->expects($this->any())
            ->method('findBy')
            ->willReturn([$actUserMock]);
        $objectManager = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $kernel = $this
            ->getMockBuilder(KernelInterface::class)
            ->setMethods(array('getProjectDir'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $documentService = $this->getMockBuilder(DocumentService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailService = $this->getMockBuilder(MailService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $orderService = $this->getMockBuilder(OrderService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $serializer = new Serializer(array(new ObjectNormalizer()));
        $data = $serializer->normalize($actMock,null);
        $data["requestDate"]=new \DateTime();
        $repositoryMock->expects($this->any())
            ->method('findActById')
            ->willReturn($data);
        $objectManager->expects($this->any())
            ->method('getRepository')
            ->withAnyParameters()
            ->willReturn($repositoryMock);


        $actService = new ActService($objectManager, $orderService, $mailService, $documentService, $kernel);
        $res = $actService->refuseAct(['actId'=>1,'id'=>1]);

        $this->assertIsArray($res);
    }

    public function testRefuseSignature()
    {
        $date = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);

        $actUserMock = new ActUser();
        $userMock = new User();
        $userMock->setLastName('tayari');
        $userMock->setName('fares');
        $archiveMock = new Archive();
        $actMock = new Act();
        $actMock->setName('test');
        $actMock->setStatus('En projet');
        $actMock->setFolderName('test');
        $actMock->setFolderNumber('123456');
        $actMock->setRequestDate(new \DateTime());
        $returnActUserMock=$this->getMockBuilder(ActUser::class)
            ->setMethods(array('getUser'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $returnActUserMock->expects($this->any())
            ->method('getUser')
            ->willReturn($userMock);
        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('findOneBy','find'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $repositoryMock->expects($this->any())
            ->method('findOneBy')
            ->willReturn($returnActUserMock);
        $repositoryMock->expects($this->any())
            ->method('find')
            ->willReturn($actMock);
        $objectManager = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $kernel = $this
            ->getMockBuilder(KernelInterface::class)
            ->setMethods(array('getProjectDir'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $documentService = $this->getMockBuilder(DocumentService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailService = $this->getMockBuilder(MailService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $orderService = $this->getMockBuilder(OrderService::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $objectManager->expects($this->any())
            ->method('getRepository')
            ->withAnyParameters()
            ->willReturn($repositoryMock);

        $actService = new ActService($objectManager, $orderService, $mailService, $documentService, $kernel);

        $res = $actService->refuseSignature(['actId'=>1,'id'=>1]);

        $this->assertTrue($res);

    }

}