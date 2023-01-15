<?php


namespace App\Controller\Api;


use App\Entity\CguUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiBasicUserController
 * @package App/Controller/Api
 * @Route("/api/environment",name="api_env")
 */

class ApiEnvConstroller extends AbstractController
{
    /**
     *@Route("/variabels" ,name="get_env_vars",options={"expose"=true},methods={"GET"})
     *
     */
    public function getEnvironmentVars ()
    {
        $em = $this->getDoctrine()->getManager();
        $showModalCgu=false;
        $showModalPc=false;
        $showModal=false;
        $lastCgu=null;
        $lastPc=null;
        $updateDate = $_ENV['CGU_DATE_AVOCAT'];
        $version = $_ENV['CGU_VERSION_AVOCAT'];
        $cguText = $_ENV['CGU_TEXT_AVOCAT'];
        $role = $this->getUser()->getRoles()[0];
        $cguUser = $em->getRepository(CguUser::class)->findBy(array('user' => $this->getUser(), 'type' => 'cgu'), array('date' => 'DESC'));
        if (sizeof($cguUser) == 0) {
            $showModalCgu = true;
        } else {
            if ($cguUser[0]->getVersion() < $_ENV['CGU_VERSION_AVOCAT']) {
                $showModalCgu = true;
            }
            $lastCgu = $cguUser[0]->getDate()->format('d/m/Y');
        }
        $cguUserPc = $em->getRepository(CguUser::class)->findBy(array('user' => $this->getUser(), 'type' => 'pc'), array('date' => 'DESC'));
        if (sizeof($cguUserPc) == 0) {
            $showModalPc = true;
        } else {
            if ($cguUserPc[0]->getVersion() < $_ENV['PC_VERSION_AVOCAT']) {
                $showModalPc = true;
            }
            $lastPc = $cguUserPc[0]->getDate()->format('d/m/Y');
        }
        if ($this->getUser()->getRoles()[0] == 'ROLE_COUNSEL') {
            $updateDate = $_ENV['CGU_DATE_OTP'];
            $version = $_ENV['CGU_VERSION_OTP'];
            $cguText = $_ENV['CGU_TEXT_OTP'];
        }
        if ($this->getUser()->getRoles()[0] == 'ROLE_SIGNATORY') {
            $updateDate = $_ENV['CGU_DATE_SIG_OTP'];
            $version = $_ENV['CGU_VERSION_SIG_OTP'];
            $cguText = $_ENV['CGU_TEXT_SIG_OTP'];
        }
        if ($this->getUser()->getIpaddress() == null) {
            $showModal = true;
        }
        $vars=array('showModalCgu' => $showModalCgu,'showModalPc' => $showModalPc,'cguText' => $cguText
        , 'version' => $version, 'versionPc' => $_ENV['PC_VERSION_AVOCAT'], 'updateDate' => $updateDate, 'lastCgu' => $lastCgu
        , 'updateDatePc' => $_ENV['PC_DATE_AVOCAT'], 'pcText' => $_ENV['PC_TEXT_AVOCAT'],'lastPc' => $lastPc,'env'=>$_ENV['APP_ENV'],'deadLine'=>$_ENV['MAIL_VALIDITY'], 'role'=>$role);
        return new JsonResponse($vars,200);
    }
}