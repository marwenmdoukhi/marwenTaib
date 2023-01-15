<?php
/**
 * Created by PhpStorm.
 * User: MAZ-USER5
 * Date: 07/11/2019
 * Time: 11:55
 */

namespace App\Controller\Api;


use App\Entity\Act;
use App\Entity\ActUser;
use App\Entity\CguUser;
use App\Entity\Contact;
use App\Entity\Document;
use App\Entity\Settings;
use App\Entity\User;
use App\Service\CounselService;
use App\Service\MailService;
use App\Service\SignatoryService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


/**
 * Class ApiUserController
 * @package App/Controller/Api
 * @Route("/api/users",name="api_user_")
 */
class ApiUserController extends AbstractController
{
    /**
     * @var SignatoryService
     */
    private $signatoryService;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var CounselService
     */
    private $counselService;
    private $mailService;
    private $projectDir;

    public function __construct(EntityManagerInterface $entityManager, SignatoryService $signatoryService, CounselService $counselService, MailService $mailService, KernelInterface $kernel)
    {
        $this->signatoryService = $signatoryService;
        $this->entityManager = $entityManager;
        $this->counselService = $counselService;
        $this->mailService = $mailService;
        $this->projectDir = $kernel->getProjectDir();
    }

    /**
     * @Route("/connected",name="connected_user",options={"expose"=true},methods={"GET"})
     * @return JsonResponse
     */
    public function getConnectedUserAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $act = $request->get('act');
        $userId = $this->getUser()->getId();
        $user = $this->getUser();
        if ($act != null) {
            $actObj = $this->entityManager->getRepository(Act::class)->find($act);
            $result = $this->entityManager->getRepository(User::class)->findUserConnectedOtp($userId, $actObj->getInitiator()->getId());
            $result[0]['roles'] = $user->getRoles();
            return new JsonResponse($result[0], 200);
        } else {
            $result = $this->entityManager->getRepository(User::class)->findUserConnected($userId);
            $result['roles'] = $user->getRoles();
        }

        return new JsonResponse($result, 200);
    }

    /**
     * @Route("/get-contact",name="get_contact",options={"expose"=true},methods={"GET"})
     * @return JsonResponse
     */
    public function getContactAction(): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $userId = $this->getUser()->getId();
        $result = $this->entityManager->getRepository(User::class)->findContactSignatoryByUser($userId);
        $result1 = $this->entityManager->getRepository(User::class)->findContactCounselByUser($userId);
        //$result2 = $this->entityManager->getRepository(User::class)->findContactEdentitasByUser($userId);
        $users = array_merge($result1, $result);
        $usersJson = [];
        foreach ($users as $u) {
            $u['birthDate'] = $u['birthDate'] != null ? $u['birthDate']->format('d/m/Y') : null;
            $u['modificationDate'] = $u['modificationDate'] != null ? $u['modificationDate']->format('d/m/Y H:i') : null;
            array_push($usersJson, $u);
        }
        $result = $this->unique_multidimensional_array($usersJson, 'id');

        return new JsonResponse($result, 200);
    }

    /**
     * @Route("/",name="new",options={"expose"=true},methods={"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function newContactAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $result = false;
        $resultCounsel = false;
        $user = $this->getUser();
        $basicUserArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($basicUserArray) ? $basicUserArray : array());
        if ($basicUserArray == '') {
            return new JsonResponse('no body found', 404);
        }
        if ($basicUserArray['role'] == 'signatory' or $basicUserArray['role'] == 'enterprise') {
            $result = $this->signatoryService->newContact($basicUserArray, $user);

        }
        if ($result != false) {
            $result['birthDate'] = $result['birthDate'] != null ? $result['birthDate']->format('d/m/Y') : null;
            return new JsonResponse($result, 200);
        }
        if ($basicUserArray['role'] == 'counsel') {
            $resultCounsel = $this->counselService->newContact($basicUserArray, $user);
        }
        if ($resultCounsel != false) {
            $resultCounsel['birthDate'] = $resultCounsel['birthDate'] != null ? $resultCounsel['birthDate']->format('d/m/Y') : null;
            return new JsonResponse($resultCounsel, 200);
        }
        return new JsonResponse('error while adding user ', 500);
    }

    /**
     * @Route("/",name="delete",options={"expose"=true},methods={"DELETE"})
     * @param $request
     * @return JsonResponse
     */
    public function deleteContactAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $user = json_decode($request->getContent(), true);
        $request->request->replace(is_array($user) ? $user : array());
        if ($user == '') {
            return new JsonResponse('no body found', 404);
        }
        $result = $this->counselService->deleteContact($user);
        if ($result == 'user deleted') {
            return new JsonResponse($result, 200);
        }

        return new JsonResponse($result, 200);
    }

    /**
     * @Route("/",name="update",options={"expose"=true},methods={"PUT"})
     * @param $request
     * @return JsonResponse
     */
    public function updateContactAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $result = false;
        $resultCounsel = false;
        $basicUserArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($basicUserArray) ? $basicUserArray : array());
        if ($basicUserArray == '') {
            return new JsonResponse('no body found', 404);
        }
        if ($basicUserArray['role'] == 'signatory' or $basicUserArray['role'] == 'enterprise') {
            $result = $this->signatoryService->updateContact($basicUserArray);
        }
        if ($result != false) {
            $result['birthDate'] = $result['birthDate'] != null ? $result['birthDate']->format('d/m/Y') : null;
            return new JsonResponse($result, 200);
        }
        if ($basicUserArray['role'] == 'counsel') {
            $resultCounsel = $this->counselService->updateContact($basicUserArray);
        }
        if ($resultCounsel != false) {
            $resultCounsel['birthDate'] = $resultCounsel['birthDate'] != null ? $resultCounsel['birthDate']->format('d/m/Y') : null;
            return new JsonResponse($resultCounsel, 200);
        }
        return new JsonResponse('error while updating user ', 500);
    }

    /**
     * @Route("/accept-cgu",name="accept_cgu",options={"expose"=true},methods={"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function acceptCguAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $user = $this->getUser();
        $date = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);
        $cguUser = new CguUser();
        $cguUser->setUser($user);
        $cguUser->setType('cgu');
        $cguUser->setDate($date);
        $cguUser->setVersion($_ENV['CGU_VERSION_AVOCAT']);
        $user->setResiliation(null);
        $this->entityManager->persist($cguUser);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse('cgu accepted', 200);
    }

    /**
     * @Route("/accept-pc",name="accept_pc",options={"expose"=true},methods={"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function acceptPcAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $date = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);
        $cguUser = new CguUser();
        $cguUser->setUser($this->getUser());
        $cguUser->setType('pc');
        $cguUser->setDate($date);
        $cguUser->setVersion($_ENV['PC_VERSION_AVOCAT']);
        $this->entityManager->persist($cguUser);
        $this->entityManager->flush();

        return new JsonResponse('pc accepted', 200);
    }

    private function unique_multidimensional_array($array, $key)
    {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                array_push($temp_array, $val);
            }
            $i++;
        }
        return $temp_array;
    }

    /**
     * @Route("/manuals" , name="manuals",options={"expose"=true} , methods={"GET"})
     * @return JsonResponse
     */
    public function faqPage(): JsonResponse
    {
        $manual = $this->entityManager->getRepository(Settings::class)->getManuals();

        return new JsonResponse($manual, 200);
    }

    /**
     * @Route("/signed-doc-otp",name="otpDoc",options={"expose"=true},methods={"POST"})
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function downloadOtpsigned(Request $request)
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $actId = $request->getContent();
        $arr = json_decode($request->getContent(), true);
        $actId = $arr[0];
        $oldTimestamp = $arr[1];
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        if (abs($timestamp - $oldTimestamp) / 60 / 60 / 24 >= floatval($_ENV['DOWNLOAD_VALIDITY'])) {
            return $this->render('home/error.html.twig');
        }
        $act = $this->entityManager->getRepository(Act::class)->find($actId);
        $filName = $act->getFolderNumber();
        $result = $filName;
        return new JsonResponse($result, 200);

    }

    /**
     * @Route("/resiliation",name="resiliation",options={"expose"=true},methods={"POST"})
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function resiliationAction(Request $request)
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $arr = json_decode($request->getContent(), true);
        $user = $this->entityManager->getRepository(User::class)->find($arr[0]['id']);
        $reason = $arr[1];
        $cgu = $this->entityManager->getRepository(CguUser::class)->findOneBy(array('user' => $user, 'type' => 'cgu', 'version' => $_ENV['CGU_VERSION_AVOCAT']));
        $date = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);
        $user->setResiliation($date);
        $user->setReason($reason);
        $this->entityManager->persist($user);
        if ($cgu != null) {
            $this->entityManager->remove($cgu);
        }
        $this->entityManager->flush();

        return new JsonResponse('resiliation', 200);

    }

    /**
     * @Route("/extract/{cnb}",name="extract",options={"expose"=true},methods={"GET"})
     * @param int $cnb
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function ExtractDataAction(int $cnb)
    {
        $documents = [];
        $actUsers = [];
        $user = $this->entityManager->getRepository(User::class)->findOneBy(array('cnbId' => $cnb));
        if ($user) {
            $contacts = $this->entityManager->getRepository(Contact::class)->findContactForCSVById($user);
            $acts = $this->entityManager->getRepository(Act::class)->findActCSV($user);
            foreach ($acts as $act) {
                $document = $this->entityManager->getRepository(Document::class)->findDocByActIdCSV($act['id']);
                $actUser = $this->entityManager->getRepository(ActUser::class)->findActUsersCSV($act['id'], $user);
                foreach ($document as $doc) {
                    array_push($documents, $doc);
                }
                foreach ($actUser as $au) {
                    array_push($actUsers, $au);
                }
            }
            if (!is_dir($cnb)) {
                mkdir($cnb, 777);
            }
            file_put_contents(
                $cnb . '/contact.csv',
                $this->get('serializer')->encode($contacts, 'csv')
            );
            file_put_contents(
                $cnb . '/act.csv',
                $this->get('serializer')->encode($acts, 'csv')
            );
            file_put_contents(
                $cnb . '/document.csv',
                $this->get('serializer')->encode($documents, 'csv')
            );
            file_put_contents(
                $cnb . '/actUser.csv',
                $this->get('serializer')->encode($actUsers, 'csv')
            );
            $zip = new \ZipArchive();
            if ($zip->open($cnb . '.zip', \ZipArchive::CREATE) === true) {
                $zip->addFile($cnb . '/contact.csv');
                $zip->addFile($cnb . '/act.csv');
                $zip->addFile($cnb . '/document.csv');
                $zip->addFile($cnb . '/actUser.csv');
                $zip->close();
                unlink($cnb . '/contact.csv');
                unlink($cnb . '/act.csv');
                unlink($cnb . '/document.csv');
                unlink($cnb . '/actUser.csv');
                rmdir($cnb);
                header("Content-type: application/zip");
                header("Content-Disposition: attachment; filename = " . $cnb . ".zip");
                header("Pragma: no-cache");
                header("Expires: 0");
                readfile($cnb . '.zip');
                unlink($cnb . '.zip');
            }
            foreach ($documents as $doc) {
                $document = $this->entityManager->getRepository(Document::class)->find($doc['id']);
                $this->entityManager->remove($document);
                $this->entityManager->flush();
            }
            foreach ($actUsers as $au) {
                $actUser = $this->entityManager->getRepository(ActUser::class)->find($au['id']);
                $this->entityManager->remove($actUser);
                $this->entityManager->flush();
            }
            foreach ($contacts as $c) {
                $contact = $this->entityManager->getRepository(ActUser::class)->find($c['id']);
                $this->entityManager->remove($contact);
                $this->entityManager->flush();
            }
            foreach ($acts as $a) {
                $act = $this->entityManager->getRepository(ActUser::class)->find($a['id']);
                $this->entityManager->remove($act);
                $this->entityManager->flush();
            }
            return new JsonResponse('done', 200);

        }


        return new JsonResponse('error', 500);

    }

    /**
     * @Route("/dead-line",name="dead_line",options={"expose"=true},methods={"GET"})
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function deadLineAction()
    {
        $dateNow = new DateTime();
        $subject = 'e-ASSP - Acte à signer électroniquement envoyé par votre avocat ';
        $em = $this->getDoctrine()->getManager();
        $actS = $em->getRepository(Act::class)->findBy(array('status' => 'En cours de signature'));
        $actV = $em->getRepository(Act::class)->findBy(array('status' => 'En cours de validation'));
        foreach ($actS as $act) {
            if ($act->getReceptionDate() != null) {

                if($act->getExpirationDate() != null) {
                    $date = date('d/m/Y', strtotime($act->getExpirationDate()->getTimestamp()));
                    $numberOfDayToExpiration = $act->getExpirationDate()->getTimestamp() - $dateNow->getTimestamp();
                }
                else {
                    $date = date('d/m/Y', strtotime('+' . $_ENV['MAIL_VALIDITY'] . ' days', $act->getReceptionDate()->getTimestamp()));
                    $numberOfDayToExpiration = strtotime('+' . $_ENV['MAIL_VALIDITY'] . ' days', $act->getReceptionDate()->getTimestamp()) - $dateNow->getTimestamp();
                }

                $statut = 'signature';
                $userArrayS = $em->getRepository(User::class)->findSignatoryByAct($act, $act->getInitiator());

                foreach ($userArrayS as $user) {

                    if ($user['signedAt'] == null and ($numberOfDayToExpiration / 60 / 60 / 24) <= 0 and ($numberOfDayToExpiration / 60 / 60 / 24) >= -1) {
                        $this->mailService->deadLineMessage($user, $act, $subject, $act->getInitiator(), $statut, $date);
                    }

                    if ($user['signedAt'] == null and round($numberOfDayToExpiration / 60 / 60 / 24) >= 2 and round($numberOfDayToExpiration / 60 / 60 / 24) <= 3) {
                        $status = '[Reste 2 J Pour  la signature] Acte à valider';
                        $this->mailService->sendSigningEmailMessage($act->getInitiator(), $user, $status, $act->getReceptionDate(), "2");
                    }
                }
            }
        }

        foreach ($actV as $a) {
            if ($a->getReceptionDate() != null) {
                $statut = 'validation';

                if($act->getExpirationDate() != null) {
                    $date = date('d/m/Y', strtotime($a->getExpirationDate()->getTimestamp()));
                    $numberOfDayToExpiration = $a->getExpirationDate()->getTimestamp() - $dateNow->getTimestamp();
                }
                else {
                    $date = date('d/m/Y', strtotime('+' . $_ENV['MAIL_VALIDITY'] . ' days', $a->getReceptionDate()->getTimestamp()));
                    $numberOfDayToExpiration = strtotime('+' . $_ENV['MAIL_VALIDITY'] . ' days', $a->getReceptionDate()->getTimestamp()) - $dateNow->getTimestamp();
                }

                $userArrayV = $em->getRepository(User::class)->findUserByAct($a, $a->getInitiator());
                foreach ($userArrayV as $u) {
                    if ($u['validator'] == true and $u['validatedAt'] == null and $numberOfDayToExpiration / 60 / 60 / 24 <= 0 and $numberOfDayToExpiration / 60 / 60 / 24 >= -1) {
                        $this->mailService->deadLineMessage($u, $a, $subject, $a->getInitiator(), $statut, $date);
                    }
                    if ($u['validator'] == true and $u['validatedAt'] == null and round($numberOfDayToExpiration/ 60 / 60 / 24) >= 2 and round($numberOfDayToExpiration / 60 / 60 / 24) <= 3) {
                        $status = '[Reste 2 J Pour  la validation] Acte Sous Signature privée pour validation';
                        $this->mailService->sendValidationEmailMessage($act->getInitiator(), $u, $status, $a->getReceptionDate());
                    }
                }
            }
        }

        return new JsonResponse('done', 200);
    }


}