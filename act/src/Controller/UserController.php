<?php

namespace App\Controller;

use App\Entity\Act;
use App\Entity\ActUser;
use App\Entity\Contact;
use App\Entity\Proof;
use App\Entity\User;
use App\Service\LdapService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $ldapService;
    private $projectDir;


    public function __construct(LdapService $ldapService, KernelInterface $kernel)
    {
        $this->ldapService = $ldapService;
        $this->projectDir = $kernel->getProjectDir();

    }


    /**
     * @Route("/validation", name="validation")
     * @param $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function ValidationBypass(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $date = new DateTime();
        $id = $request->query->get('id');
        $act = $request->query->get('act');
        $actObj = $em->getRepository(Act::class)->find($act);
        $timestamp = $date->getTimestamp();
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $actUser = $em->getRepository(ActUser::class)->findOneBy(array('act' => $act, 'user' => $user));
        if ($actUser->getEnabled() == 0) {
            return $this->render('home/blocked.html.twig');
        }
        if ($actUser == null) {
            return $this->render('home/error.html.twig');
        }
        $contact = $em->getRepository(Contact::class)->findOneBy(array('initiator' => $actUser->getAct()->getInitiator(), 'contact' => $user));
        $oldTimestamp = $request->query->get('timestamp');
        $oldVersion = $request->query->get('version');

        if($actObj->getExpirationDate() != null) {
            $expirationDate = $actObj->getExpirationDate();
            $numberOfDaysToExpiration = abs($timestamp - $expirationDate->getTimestamp()) / 60 / 60 / 24;
        }
        else {
            $numberOfDaysToExpiration = floatval($_ENV['MAIL_VALIDITY']);
        }

        if (abs($timestamp - $oldTimestamp) / 60 / 60 / 24 >= $numberOfDaysToExpiration or $actUser == null or $actUser->getValidatedAt() != null or $actObj->getStatus() != "En cours de validation" or $contact->getVersion() != $oldVersion) {
            return $this->render('home/error.html.twig');
        }

        if ($user != null) {
            $token = new UsernamePasswordToken($user, $user->getPassword(), "main", $user->getRoles());
            $this->get("security.token_storage")->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));
            $event = new InteractiveLoginEvent($request, $token);
            $dispatcher = new EventDispatcher();
            $dispatcher->dispatch($event);
            if ($user->getCnbId() != null) {
                return $this->redirectToRoute('act');
            }
            $env = $_ENV['APP_ENV'];
            if ($env = 'dev') {
                $envId = 31;
            } else {
                $envId = 29;
            }
            return $this->render('home/validation.html.twig', array('envId' => $envId));
        }
        return $this->render('home/error.html.twig');

    }

    /**
     * @Route("/signing", name="signing")
     * @param $request
     * @return Response
     */
    public function SigningBypass(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $id = $request->query->get('id');
        $act = $request->query->get('act');
        $actObj = $em->getRepository(Act::class)->find($act);
        $oldTimestamp = $request->query->get('timestamp');
        $user = $em->getRepository(User::class)->find($id);
        $actUser = $em->getRepository(ActUser::class)->findOneBy(array('act' => $act, 'user' => $user));
        if ($actUser->getEnabled() == 0) {
            return $this->render('home/blocked.html.twig');
        }

        if ($actUser == null) {
            return $this->render('home/error.html.twig');
        }
        $contact = $em->getRepository(Contact::class)->findOneBy(array('initiator' => $actUser->getAct()->getInitiator(), 'contact' => $user));
        $oldVersion = $request->query->get('version');

        if($actObj->getExpirationDate() != null) {
            $expirationDate = $actObj->getExpirationDate();
            $numberOfDaysToExpiration = abs($timestamp - $expirationDate->getTimestamp()) / 60 / 60 / 24;
        }
        else {
            $numberOfDaysToExpiration = floatval($_ENV['MAIL_VALIDITY']);
        }

        if (abs($timestamp - $oldTimestamp) / 60 / 60 / 24 >= $numberOfDaysToExpiration or $actUser == null or $actUser->getSignedAt() != null or $actObj->getStatus() != "En cours de signature" or $contact->getVersion() != $oldVersion) {
            return $this->render('home/error.html.twig');
        }
        if ($user != null) {
            $dateProof = new DateTime();
            $timezone = new \DateTimeZone('Europe/Paris');
            $dateProof->setTimezone($timezone);
            $token = new UsernamePasswordToken($user, $user->getPassword(), "main", $user->getRoles());
            $this->get("security.token_storage")->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));
            $event = new InteractiveLoginEvent($request, $token);
            $dispatcher = new EventDispatcher();
            $dispatcher->dispatch($event);
            $contact = $em->getRepository(Contact::class)->findOneBy(array('contact' => $user, 'initiator' => $actObj->getInitiator()));
            $proof = new Proof();
            $proof->setContact($contact);
            $proof->setDate($dateProof);
            $proof->setAct($actObj);
            $proof->setEvent('Authentification au service');
            $em->persist($proof);
            $em->flush();
            $env = $_ENV['APP_ENV'];
            if ($env = 'dev') {
                $envId = 31;
            } else {
                $envId = 29;
            }

            return $this->render('home/signing.html.twig', array('envId' => $envId));

        }
        return $this->render('home/error.html.twig');

    }

    /**
     * @Route("/download-file",name="download_file")
     * @param $request
     * @return Response
     */
    public function downloadFinalDocument(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $actId = $request->query->get('act');
        $oldTimestamp = $request->query->get('timestamp');
        if (abs($timestamp - $oldTimestamp) / 60 / 60 / 24 >= floatval($_ENV['DOWNLOAD_VALIDITY'])) {
            return $this->render('home/error.html.twig');
        }
        $act = $em->getRepository(Act::class)->find($actId);
        $filName = $act->getFolderNumber();
        header("Content-disposition: attachment; filename=" . $filName . ".pdf");
        header("Content-type: application/pdf");
        readfile("documents/" . $filName . "ForSigning.pdf");

    }

    /**
     * @Route("/informations",name="informations")
     *
     */
    public function informations()
    {
        $env = $_ENV['APP_ENV'];
        if ($env = 'dev') {
            $envId = 31;
        } else {
            $envId = 29;
        }
        return $this->render('home/informations.html.twig', array('envId' => $envId));
    }

    /**
     * @Route("/download_document", name="download-otp")
     */
    public function DownloadDocumentOtp(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $date = new DateTime();
        $id = $request->query->get('id');
        $act = $request->query->get('act');
        $actObj = $em->getRepository(Act::class)->find($act);
        $timestamp = $date->getTimestamp();
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $actUser = $em->getRepository(ActUser::class)->findOneBy(array('act' => $act, 'user' => $user));
        $oldTimestamp = $request->query->get('timestamp');
        if ($actUser->getEnabled() == 0) {
            return $this->render('home/blocked.html.twig');
        }

        if($actObj->getExpirationDate() != null) {
            $expirationDate = $actObj->getExpirationDate();
            $numberOfDaysToExpiration = abs($timestamp - $expirationDate->getTimestamp()) / 60 / 60 / 24;
        }
        else {
            $numberOfDaysToExpiration = floatval($_ENV['MAIL_VALIDITY']);
        }

        if (abs($timestamp - $oldTimestamp) / 60 / 60 / 24 >= $numberOfDaysToExpiration or $actUser == null or $actUser->getSignedAt() == null or $actObj->getStatus() == "Abandonne" or $actObj->getStatus() == "Validation refusee") {
            return $this->render('home/error.html.twig');
        }

        if ($user != null) {
            $token = new UsernamePasswordToken($user, $user->getPassword(), "main", $user->getRoles());
            $this->get("security.token_storage")->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));
            $event = new InteractiveLoginEvent($request, $token);
            $dispatcher = new EventDispatcher();
            $dispatcher->dispatch($event);
            if ($user->getCnbId() != null) {
                return $this->redirectToRoute('act');
            }
            $env = $_ENV['APP_ENV'];
            if ($env = 'dev') {
                $envId = 31;
            } else {
                $envId = 29;
            }
            return $this->render('home/downloadOtp.html.twig', array('envId' => $envId));
        }
        $env = $_ENV['APP_ENV'];
        if ($env = 'dev') {
            $envId = 31;
        } else {
            $envId = 29;
        }
        return $this->render('home/error.html.twig');
    }

    /**
     * @Route("/faq",name="faq")
     *
     */
    public function faq()
    {
        $env = $_ENV['APP_ENV'];
        if ($env = 'dev') {
            $envId = 31;
        } else {
            $envId = 29;
        }
        return $this->render('home/faq.html.twig', array('envId' => $envId));
    }


    /**
     * @Route("/",name="welcome")
     * @return Response
     */
    public function homePage()
    {
        $user = $this->getUser();
        if ($user) {
            return $this->redirectToRoute('act');
        } else {
            return $this->render('home/welcome.html.twig');
        }
    }


    /**
     * @Route("/admin",name="admin")
     *
     */
    public function zipLogs()
    {
        if ($this->getUser()->getCnbId() == "999012") {
            $finder = new Finder();
            $finder->files()->in($this->projectDir . '/var/log');
            return $this->render('home/log.html.twig', array(
                'finder' => $finder,
            ));
        }
        return $this->redirectToRoute('dashboard');

    }

    /**
     * @Route("/downloadLogFile" , name="downloadLogFile",options={"expose"=true} , methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function downloadLogFileAction(Request $request): Response
    {
        $response = new Response();
        if (substr($this->getUser()->getCnbId(), 0, 4) == "9997" or substr($this->getUser()->getCnbId(), 0, 4) == "9990") {

            $response->headers->set('Content-type', mime_content_type($this->projectDir . '/var/log/' . $request->get('name')));
            $response->headers->set("Content-Disposition", "attachment; filename = " . basename($request->get('name')));
            $response->headers->set('Cache-Control', 'private');
            $response->sendHeaders();

            $response->setContent(file_get_contents($this->projectDir . '/var/log/' . $request->get('name')));
        }


        return $response;
    }
}
