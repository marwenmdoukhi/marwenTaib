<?php

namespace App\Controller;

use App\Entity\CguUser;
use App\Entity\User;
use App\Service\OrderService;
use http\Env;
use PetstoreIO\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ActController extends AbstractController
{
    /**
     * @Route("/myact", name="act")
     */
    public function index()
    {
        if(($this->getUser()->getRoles()[0]!="ROLE_USER" and ($this->getUser()->getRoles()[0]!="ROLE_COUNSEL") or $this->getUser()->getCnbId()==null)){
            return $this->render('home/welcome.html.twig');
        }
        $lastCgu = null;
        $lastPc = null;
        $env = $_ENV['APP_ENV'];
        if ($env == 'dev'){
            $envId = 31;
        }else{
            $envId = 29;
        }
        return $this->render('act/index.html.twig', array('envId'=>$envId));
    }


    /**
     * @Route("/switch-roles", name="acts")
     */
    public function switchRoles()
    {
        $em = $this->getDoctrine()->getManager();
        $loggedUser = $this->get('security.token_storage')->getToken()->getUser();
        if ($loggedUser->getRoles()[0] == 'ROLE_USER') {
            $loggedUser->removeRole('ROLE_USER');
            $loggedUser->addRole('ROLE_COUNSEL');
            $em->persist($loggedUser);
            $em->flush();
        } else {
            $loggedUser->removeRole('ROLE_COUNSEL');
            $loggedUser->addRole('ROLE_USER');
            $em->persist($loggedUser);
            $em->flush();
        }
        $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken(
            $loggedUser,
            null,
            'main',
            $loggedUser->getRoles()
        );
        $this->get('security.token_storage')->setToken($token);
        if ($loggedUser->getRoles()[0] == 'ROLE_USER') {
            return $this->redirectToRoute('dashboard');
        }
        return $this->redirectToRoute('dashboard_counsel');

    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contactIndex()
    {
        if(($this->getUser()->getRoles()[0]!="ROLE_USER" and ($this->getUser()->getRoles()[0]!="ROLE_COUNSEL") or $this->getUser()->getCnbId()==null)){
            return $this->render('home/welcome.html.twig');
        }
        $env = $_ENV['APP_ENV'];
        if ($env == 'dev'){
            $envId = 31;
        }else{
            $envId = 29;
        }
        return $this->render('act/contact.html.twig',['envId'=>$envId]);
    }
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboardIndex()
    {
        if(($this->getUser()->getRoles()[0]!="ROLE_USER" and ($this->getUser()->getRoles()[0]!="ROLE_COUNSEL") or $this->getUser()->getCnbId()==null)){
            return $this->render('home/welcome.html.twig');
        }
        $env = $_ENV['APP_ENV'];
        $user = $this->getUser();
        if ($env == 'dev'){
            $envId = 31;
        }else{
            $envId = 29;
        }
        return $this->render('act/dashboard.html.twig', array('user'=>$user,'envId'=>$envId));
    }
    /**
     * @Route("/dashboard-counsel", name="dashboard_counsel")
     */
    public function dashboardCounselIndex()
    {
        if(($this->getUser()->getRoles()[0]!="ROLE_USER" and ($this->getUser()->getRoles()[0]!="ROLE_COUNSEL") or $this->getUser()->getCnbId()==null)){
            return $this->render('home/welcome.html.twig');
        }
        $env = $_ENV['APP_ENV'];
        if ($env == 'dev'){
            $envId = 31;
        }else{
            $envId = 29;
        }
        return $this->render('act/dashboardCounsel.html.twig',['envId'=>$envId]);
    }

}
