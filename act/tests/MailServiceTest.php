<?php


namespace App\Tests;


use App\Entity\Act;
use App\Entity\Contact;
use App\Entity\User;
use App\Service\MailService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class MailServiceTest extends TestCase
{
    public function testSendValidationEmailMessage()
    {
        $userMock = new User();
        $returnUserMock=$this->getMockBuilder(User::class)
            ->setMethods(array('getId'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $returnUserMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(5760));
        $ContactMock = $this->getMockBuilder(Contact::class)
            ->setMethods(array('getContact'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $ContactMock->expects($this->any())
            ->method('getContact')
            ->willReturn($returnUserMock);

        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('findOneBy'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('findOneBy')
            ->willReturn($ContactMock);
        $objectManager = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $objectManager->expects($this->any())
            ->method('getRepository')
            ->withAnyParameters()
            ->willReturn($repositoryMock);
        $templating = $this
            ->getMockBuilder(Environment::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailer = $this
            ->getMockBuilder(\Swift_Mailer::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $security = $this
            ->getMockBuilder(Security::class)
            ->setMethods(array('getUser'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailService = new MailService($mailer, $templating, $objectManager, $security);
        $res = $mailService->sendValidationEmailMessage($userMock, ['id' => 5760, 'actId' => 4, 'email' => 'helmi.bejaoui@yellow-it.fr'], 'Acte à valider');
        $this->assertEquals(true, $res);
    }

    public function testSendSigningEmailMessage()
    {
        $actMock = $this->getMockBuilder(User::class)
            ->setMethods(array('getInitiator'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $actMock->expects($this->any())
            ->method('getInitiator')
            ->will($this->returnValue(1));
        $userMock = new User();
        $returnUserMock = $this->getMockBuilder(User::class)
            ->setMethods(array('getId'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $returnUserMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(5760));
        $ContactMock = $this->getMockBuilder(Contact::class)
            ->setMethods(array('getContact'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $ContactMock->expects($this->any())
            ->method('getContact')
            ->willReturn($returnUserMock);

        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('findOneBy'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('findOneBy')
            ->willReturn($ContactMock);
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
        $templating = $this
            ->getMockBuilder(Environment::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailer = $this
            ->getMockBuilder(\Swift_Mailer::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $security = $this
            ->getMockBuilder(Security::class)
            ->setMethods(array('getUser'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailService = new MailService($mailer, $templating, $objectManager, $security);
        $res = $mailService->sendSigningEmailMessage($userMock, ['id' => 5760, 'actId' => 4, 'email' => 'helmi.bejaoui@yellow-it.fr'], 'Acte à valider', null, '21');
        $this->assertEquals(true, $res);
    }

    public function testSendSignNotificationEmail()
    {
        $actMock = $this->getMockBuilder(User::class)
            ->setMethods(array('getInitiator'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $actMock->expects($this->any())
            ->method('getInitiator')
            ->will($this->returnValue(1));
        $userMock = new User();
        $userMock->setEmail('helmi.bejaoui@yellow-it.fr');
        $userMock->setName('helmi');
        $userMock->setLastName('helmi');
        $returnUserMock = $this->getMockBuilder(User::class)
            ->setMethods(array('getId'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $returnUserMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(5760));
        $ContactMock = $this->getMockBuilder(Contact::class)
            ->setMethods(array('getContact'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $ContactMock->expects($this->any())
            ->method('getContact')
            ->willReturn($returnUserMock);

        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('findOneBy'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('findOneBy')
            ->willReturn($ContactMock);
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
        $templating = $this
            ->getMockBuilder(Environment::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailer = $this
            ->getMockBuilder(\Swift_Mailer::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $security = $this
            ->getMockBuilder(Security::class)
            ->setMethods(array('getUser'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailService = new MailService($mailer, $templating, $objectManager, $security);
        $res = $mailService->sendSignNotificationEmail($userMock, $userMock, 'Acte à valider', $actMock);
        $this->assertEquals(true, $res);
    }

    public function testSendValiationNotifToInitiator()
    {
        $actUserMock = $this->getMockBuilder(User::class)
            ->setMethods(array('getUser', 'getAct'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $actUserMock->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue(1));
        $act =  $this->getMockBuilder(ArrayCollection::class)
            ->setMethods(array('getName'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $act->expects($this->any())
            ->method('getName')
            ->willReturn('aaa');

        $actMock = $this->getMockBuilder(User::class)
            ->setMethods(array('getInitiator', 'getName'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $actMock->expects($this->any())
            ->method('getInitiator')
            ->will($this->returnValue(1));
        $actMock->expects($this->any())
            ->method('getInitiator')
            ->will($this->returnValue(1));
        $actUserMock->expects($this->any())
            ->method('getAct')
            ->willReturn($act);


        $userMock = new User();
        $userMock->setEmail('helmi.bejaoui@yellow-it.fr');
        $userMock->setName('helmi');
        $userMock->setLastName('helmi');
        $returnUserMock = $this->getMockBuilder(User::class)
            ->setMethods(array('getId'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $returnUserMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(5760));
        $ContactMock = $this->getMockBuilder(Contact::class)
            ->setMethods(array('getContact'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $ContactMock->expects($this->any())
            ->method('getContact')
            ->willReturn($returnUserMock);

        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('findOneBy'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('findOneBy')
            ->willReturn($ContactMock);
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
        $templating = $this
            ->getMockBuilder(Environment::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailer = $this
            ->getMockBuilder(\Swift_Mailer::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $security = $this
            ->getMockBuilder(Security::class)
            ->setMethods(array('getUser'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailService = new MailService($mailer, $templating, $objectManager, $security);
        $res = $mailService->sendValiationNotifToInitiator($userMock, [$actUserMock], 'Acte à valider');
        $this->assertEquals(true, $res);
    }
    public function testDownloadFileEmailMessage()
    {
        $actMock = $this->getMockBuilder(User::class)
            ->setMethods(array('getInitiator'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $actMock->expects($this->any())
            ->method('getInitiator')
            ->will($this->returnValue(1));
        $userMock = new User();
        $returnUserMock = $this->getMockBuilder(User::class)
            ->setMethods(array('getId'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $returnUserMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(5760));
        $ContactMock = $this->getMockBuilder(Contact::class)
            ->setMethods(array('getContact'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $ContactMock->expects($this->any())
            ->method('getContact')
            ->willReturn($returnUserMock);

        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('findOneBy'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('findOneBy')
            ->willReturn($ContactMock);
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
        $templating = $this
            ->getMockBuilder(Environment::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailer = $this
            ->getMockBuilder(\Swift_Mailer::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $security = $this
            ->getMockBuilder(Security::class)
            ->setMethods(array('getUser'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailService = new MailService($mailer, $templating, $objectManager, $security);
        $res = $mailService->downloadFileEmailMessage($userMock, ['id' => 5760, 'actId' => 4, 'email' => 'helmi.bejaoui@yellow-it.fr'], 1,'Acte à valider');
        $this->assertEquals(true, $res);
    }
    public function testAllSignedNotificationToInitiator()
    {
        $actMock = $this->getMockBuilder(User::class)
            ->setMethods(array('getInitiator'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $actMock->expects($this->any())
            ->method('getInitiator')
            ->will($this->returnValue(1));
        $userMock = new User();
        $returnUserMock = $this->getMockBuilder(User::class)
            ->setMethods(array('getId'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $returnUserMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(5760));
        $ContactMock = $this->getMockBuilder(Contact::class)
            ->setMethods(array('getContact'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $ContactMock->expects($this->any())
            ->method('getContact')
            ->willReturn($returnUserMock);

        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('findOneBy'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('findOneBy')
            ->willReturn($ContactMock);
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
        $templating = $this
            ->getMockBuilder(Environment::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailer = $this
            ->getMockBuilder(\Swift_Mailer::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $security = $this
            ->getMockBuilder(Security::class)
            ->setMethods(array('getUser'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailService = new MailService($mailer, $templating, $objectManager, $security);
        $res = $mailService->allSignedNotificationToInitiator($userMock, [['id' => 5760, 'act' => 4, 'email' => 'helmi.bejaoui@yellow-it.fr','name'=>'helmi','lastName'=>'helmi']], 'Acte à valider', 'status');
        $this->assertEquals(true, $res);
    }
    public function testNotValidationNotificationToInitiator()
    {
        $actUserMock = $this->getMockBuilder(User::class)
            ->setMethods(array('getUser', 'getAct'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $actUserMock->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue(1));
        $act =  $this->getMockBuilder(ArrayCollection::class)
            ->setMethods(array('getName'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $act->expects($this->any())
            ->method('getName')
            ->willReturn('aaa');

        $actMock = $this->getMockBuilder(User::class)
            ->setMethods(array('getInitiator', 'getName'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $actMock->expects($this->any())
            ->method('getInitiator')
            ->will($this->returnValue(1));
        $actMock->expects($this->any())
            ->method('getInitiator')
            ->will($this->returnValue(1));
        $actUserMock->expects($this->any())
            ->method('getAct')
            ->willReturn($act);


        $userMock = new User();
        $userMock->setEmail('helmi.bejaoui@yellow-it.fr');
        $userMock->setName('helmi');
        $userMock->setLastName('helmi');
        $returnUserMock = $this->getMockBuilder(User::class)
            ->setMethods(array('getId'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $returnUserMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(5760));
        $ContactMock = $this->getMockBuilder(Contact::class)
            ->setMethods(array('getContact'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $ContactMock->expects($this->any())
            ->method('getContact')
            ->willReturn($returnUserMock);

        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('findOneBy'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('findOneBy')
            ->willReturn($ContactMock);
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
        $templating = $this
            ->getMockBuilder(Environment::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailer = $this
            ->getMockBuilder(\Swift_Mailer::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $security = $this
            ->getMockBuilder(Security::class)
            ->setMethods(array('getUser'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailService = new MailService($mailer, $templating, $objectManager, $security);
        $res = $mailService->NotValidationNotificationToInitiator($userMock, [$actUserMock], 'Acte à valider');
        $this->assertEquals(true, $res);
    }
    public function testSendBlockedUserToInitiator()
    {
        $objectManager = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(array('getRepository'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $templating = $this
            ->getMockBuilder(Environment::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailer = $this
            ->getMockBuilder(\Swift_Mailer::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $security = $this
            ->getMockBuilder(Security::class)
            ->setMethods(array('getUser'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailService = new MailService($mailer, $templating, $objectManager, $security);
        $res = $mailService->sendBlockedUserToInitiator('helmi.bejaoui@yellow-it.fr', 'helmu','helmi', 'blocked');
        $this->assertEquals(true, $res);
    }
    public function testDeadLineMessage()
    {
        $actMock = $this->getMockBuilder(User::class)
            ->setMethods(array('getInitiator'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $actMock->expects($this->any())
            ->method('getInitiator')
            ->will($this->returnValue(1));
        $userMock = new User();
        $userMock->setEmail('helmi.bejaoui@yellow-it.fr');
        $userMock->setName('helmi');
        $userMock->setLastName('helmi');
        $returnUserMock = $this->getMockBuilder(User::class)
            ->setMethods(array('getId'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $returnUserMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(5760));
        $ContactMock = $this->getMockBuilder(Contact::class)
            ->setMethods(array('getContact'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $ContactMock->expects($this->any())
            ->method('getContact')
            ->willReturn($returnUserMock);

        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('findOneBy'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('findOneBy')
            ->willReturn($ContactMock);
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
        $templating = $this
            ->getMockBuilder(Environment::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailer = $this
            ->getMockBuilder(\Swift_Mailer::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $security = $this
            ->getMockBuilder(Security::class)
            ->setMethods(array('getUser'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailService = new MailService($mailer, $templating, $objectManager, $security);
        $res = $mailService->deadLineMessage(['name'=>'helmi','lastName'=>'helmi'],$actMock,'DeadLine', $userMock,'status','23/11/2020' );
        $this->assertEquals(true, $res);
    }
    public function testSendRefuseValidatorNotificationToInitiator()
    {
        $actMock = $this->getMockBuilder(User::class)
            ->setMethods(array('getInitiator'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $actMock->expects($this->any())
            ->method('getInitiator')
            ->will($this->returnValue(1));
        $userMock = new User();
        $userMock->setEmail('helmi.bejaoui@yellow-it.fr');
        $userMock->setName('helmi');
        $userMock->setLastName('helmi');
        $returnUserMock = $this->getMockBuilder(User::class)
            ->setMethods(array('getId'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $returnUserMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(5760));
        $ContactMock = $this->getMockBuilder(Contact::class)
            ->setMethods(array('getContact'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $ContactMock->expects($this->any())
            ->method('getContact')
            ->willReturn($returnUserMock);

        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('findOneBy'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('findOneBy')
            ->willReturn($ContactMock);
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
        $templating = $this
            ->getMockBuilder(Environment::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailer = $this
            ->getMockBuilder(\Swift_Mailer::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $security = $this
            ->getMockBuilder(Security::class)
            ->setMethods(array('getUser'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailService = new MailService($mailer, $templating, $objectManager, $security);
        $res = $mailService->sendRefuseValidatorNotificationToInitiator($userMock, $userMock, 'Acte à valider', $actMock);
        $this->assertEquals(true, $res);
    }
    public function testSendValidatorNotificationToInitiator()
    {
        $actMock = $this->getMockBuilder(User::class)
            ->setMethods(array('getInitiator'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $actMock->expects($this->any())
            ->method('getInitiator')
            ->will($this->returnValue(1));
        $userMock = new User();
        $userMock->setEmail('helmi.bejaoui@yellow-it.fr');
        $userMock->setName('helmi');
        $userMock->setLastName('helmi');
        $returnUserMock = $this->getMockBuilder(User::class)
            ->setMethods(array('getId'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $returnUserMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(5760));
        $ContactMock = $this->getMockBuilder(Contact::class)
            ->setMethods(array('getContact'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $ContactMock->expects($this->any())
            ->method('getContact')
            ->willReturn($returnUserMock);

        $repositoryMock = $this
            ->getMockBuilder(ObjectRepository::class)
            ->setMethods(array('findOneBy'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $repositoryMock->expects($this->any())
            ->method('findOneBy')
            ->willReturn($ContactMock);
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
        $templating = $this
            ->getMockBuilder(Environment::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailer = $this
            ->getMockBuilder(\Swift_Mailer::class)
            ->setMethods(array('render'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $security = $this
            ->getMockBuilder(Security::class)
            ->setMethods(array('getUser'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mailService = new MailService($mailer, $templating, $objectManager, $security);
        $res = $mailService->sendValidatorNotificationToInitiator($userMock, $userMock, 'Acte à valider', $actMock);
        $this->assertEquals(true, $res);
    }

}