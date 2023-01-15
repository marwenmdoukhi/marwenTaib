<?php


namespace App\Tests;


use App\Entity\Act;
use App\Entity\Document;
use App\Entity\User;
use App\Service\DocumentService;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Twig\Environment;


class DocumentServiceTest extends TestCase
{
    public function testGetDocumentsByAct(){
        $documentMock=new Document();
        $documentMock->setName('test');
        $documentMock->setSize(1.565);
        $documentMock->setType('test');
        $documentMock->setConvertedType('pdf');
        $documentMock->setPosition(1);
        $documentMock->setStatus('CREATED');
        $serializer = new Serializer(array(new ObjectNormalizer()));
        $data = $serializer->normalize($documentMock,null);
        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('findDocumentsByAct'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('findDocumentsByAct')
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
        $templating = $this
            ->getMockBuilder(Environment::class)
            ->setMethods(array('getProjectDir'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $snappy = $this
            ->getMockBuilder(Pdf::class)
            ->setMethods(array('getProjectDir'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();


        $documentService= new DocumentService($objectManager,$snappy,$templating,$kernel);
        $res = $documentService->getDocumentsByAct(1);
        $this->assertNotEmpty($res);
    }
    public function testPositionDocument(){
        $documentMock=new Document();
        $documentMock->setName('test');
        $documentMock->setSize(1.565);
        $documentMock->setType('test');
        $documentMock->setConvertedType('pdf');
        $documentMock->setPosition(1);
        $documentMock->setStatus('CREATED');
        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('find'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('find')
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
        $templating = $this
            ->getMockBuilder(Environment::class)
            ->setMethods(array('getProjectDir'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $snappy = $this
            ->getMockBuilder(Pdf::class)
            ->setMethods(array('getProjectDir'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $documentService= new DocumentService($objectManager,$snappy,$templating,$kernel);
        $res=$documentService->positionDocument([['id'=>1,'position'=>1]]);
        $this->assertEquals(true,$res);
    }
    public function testPdfFrontPageIfDoesFileExist(){
        $userMock=new User();
        $actMock=new Act();
        $actMock->setFolderName('a-test');
        $actMock->setFolderNumber('A-588-1811');
        $actMock->setInitiator($userMock);


        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('findSignatoryByAct'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('findSignatoryByAct')
            ->willReturn([$userMock]);
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
        $kernel->expects($this->any())
            ->method('getProjectDir')
            ->withAnyParameters()
            ->willReturn('C:\Users\bejao\newgit\acte_sous_signature_privee');
        $templating = $this
            ->getMockBuilder(Environment::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $snappy = $this
            ->getMockBuilder(Pdf::class)
            ->setMethods(array('generateFromHtml'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $documentService= new DocumentService($objectManager,$snappy,$templating,$kernel);
        $res=$documentService->pdfFrontPage($actMock);
        $this->assertIsString($res);
    }
    public function testPdfFrontPageIfFileExist(){
        $userMock=new User();
        $actMock=new Act();
        $actMock->setFolderName('a-test');
        $actMock->setFolderNumber('A-3-0710');
        $actMock->setInitiator($userMock);


        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('findSignatoryByAct'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('findSignatoryByAct')
            ->willReturn([$userMock]);
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
        $kernel->expects($this->any())
            ->method('getProjectDir')
            ->withAnyParameters()
            ->willReturn('C:\Users\bejao\newgit\acte_sous_signature_privee');
        $templating = $this
            ->getMockBuilder(Environment::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $snappy = $this
            ->getMockBuilder(Pdf::class)
            ->setMethods(array('generateFromHtml'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $documentService= new DocumentService($objectManager,$snappy,$templating,$kernel);
        $res=$documentService->pdfFrontPage($actMock);
        $this->assertIsString($res);
    }
    /*public function testDeleteDocument(){
        $documentMock=new Document();
        $actMock=new Act();
        $userMock=new User();
        $actMock->setInitiator($userMock);
        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('findByDocumentByNameAndActId','find'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('findByDocumentByNameAndActId')
            ->willReturn($documentMock);
        $repositoryMock->expects($this->any())
            ->method('find')
            ->willReturn($actMock);
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
        $kernel->expects($this->any())
            ->method('getProjectDir')
            ->withAnyParameters()
            ->willReturn('C:\Users\bejao\newgit\acte_sous_signature_privee');
        $templating = $this
            ->getMockBuilder(Environment::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $snappy = $this
            ->getMockBuilder(Pdf::class)
            ->setMethods(array('generateFromHtml'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $documentService= new DocumentService($objectManager,$snappy,$templating,$kernel);
        $res=$documentService->deleteDocument(["name"=>'A-511-2910','actId'=>540]);
        $this->assertEquals(true,$res);
    }*/

}