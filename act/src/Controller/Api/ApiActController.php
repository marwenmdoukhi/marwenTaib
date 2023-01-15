<?php
/**
 * Created by PhpStorm.
 * User: Helmi
 * Date: 27/01/2020
 * Time: 14:19
 */

namespace App\Controller\Api;


use App\Entity\Act;
use App\Entity\ActUser;
use App\Entity\Contact;
use App\Entity\User;
use App\Service\ActService;
use App\Service\MailService;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use DateTime;
use Symfony\Component\Validator\Constraints\Json;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


/**
 * Class ApiActController
 * @package App/Controller/Api
 * @Route("/api/acts",name="api_act_")
 */
class ApiActController extends AbstractController
{
    /**
     * @var ActService
     */
    private $actService;
    /**
     * @var MailService
     */
    private $mailService;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    protected $projectDir;
    private $orderService;
    private $logger;



    public function __construct(ActService $actService, MailService $mailService, EntityManagerInterface $entityManager, KernelInterface $kernel, OrderService $orderService, LoggerInterface $appLogger)
    {
        $this->actService = $actService;
        $this->mailService = $mailService;
        $this->entityManager = $entityManager;
        $this->orderService = $orderService;
        $this->projectDir = $kernel->getProjectDir();
        $this->logger = $appLogger;
    }






    /**
     * @Route("/signing-email",name="signing_email",options={"expose"=true},methods={"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function sendSigningAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $userArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($userArray) ? $userArray : array());
        $act = $this->entityManager->getRepository(Act::class)->find($userArray[0]['actId']);
        $status = 'e-ASSP - Acte à signer électroniquement envoyé par votre avocat ';

        if ($act->getExpirationDate() !== null ) {

            $now = new DateTime();
            date();
            $difference = $act->getExpirationDate()->diff($now)->format('%a');
            $remainingDays = strval($difference);
        }
        else {
            $remainingDays = "21";
        }

        foreach ($userArray as $u) {
            $userRecepient = $this->entityManager->getRepository(User::class)->find($u['id']);
            $userAct = $this->entityManager->getRepository(ActUser::class)->findOneBy(array('act' => $u['actId'], 'user' => $userRecepient->getId()));
            if ($act->getStatus() == 'Signature refusee') {
                $userAct->setSignedAt(null);
                $userAct->setSignatureComment(null);
                $this->entityManager->persist($userAct);
                $this->entityManager->flush();
            }
            try {
                $this->mailService->sendSigningEmailMessage( $this->getUser(), $u, $status, null, $remainingDays);
            } catch (LoaderError $e) {
            } catch (RuntimeError $e) {
            } catch (SyntaxError $e) {
            }
        }
        $act = $this->entityManager->getRepository(Act::class)->find($userArray[0]['actId']);
        $act->setStatus('En cours de signature');
        $act->setReceptionDate(new DateTime());
        $this->entityManager->persist($act);
        $this->entityManager->flush();
        return new JsonResponse('email sent', 200);
    }

    /**
     * @Route("/validation-email",name="validation_email",options={"expose"=true},methods={"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function sendValidationAction(Request $request): JsonResponse
    {
        if ($this->checkRole() == true) {
            return new JsonResponse('wrong role', 400);
        }
        $date = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);
        $userArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($userArray) ? $userArray : array());
        $user = $this->getUser();
        $send = $this->mailService;
        $status = 'Acte à valider';
        $act = $this->entityManager->getRepository(Act::class)->find($userArray[0]['actId']);
        $act->setReceptionDate($date);
        $this->entityManager->persist($act);
        $this->entityManager->flush();
        foreach ($userArray as $u) {
            $contact = $this->entityManager->getRepository(Contact::class)->findOneBy(array('contact' => $u['id'], 'initiator' => $this->getUser()));
            $userRecepient = $this->entityManager->getRepository(User::class)->find($contact->getContact()->getId());
            if ($userRecepient == null) {
                $userRecepient = $this->entityManager->getRepository(Contact::class)->findOneBy(array('contact' => $u['id'], 'initiator' => $this->getUser()));
                $id = $userRecepient->getContact()->getId();
            } else {
                $id = $userRecepient->getId();
            }
            $userAct = $this->entityManager->getRepository(ActUser::class)->findOneBy(array('act' => $u['actId'], 'user' => $id));
            if ($act->getStatus() == 'Validation refusee' or $act->getStatus() == 'Signature refusee') {
                $userAct->setSignedAt(null);
                $userAct->setSignatureComment(null);
                $userAct->setValidatedAt(null);
                $userAct->setActValidated(null);
                $userAct->setComment(null);
                $this->entityManager->persist($userAct);
                $this->entityManager->flush();
                try {
                    $send->sendValidationEmailMessage($user, $u, $status);
                } catch (LoaderError $e) {
                } catch (RuntimeError $e) {
                } catch (SyntaxError $e) {
                }
            } else {
                if ($userAct->getMailSent() != true) {
                    try {
                        $send->sendValidationEmailMessage($user, $u, $status);
                    } catch (LoaderError $e) {
                    } catch (RuntimeError $e) {
                    } catch (SyntaxError $e) {
                    }
                }
            }


        }
        return new JsonResponse('email sent', 200);
    }

    /**
     * @Route("/validator",name="validator",options={"expose"=true},methods={"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function setValidatorAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }

        if ($this->checkRole() == true) {
            return new JsonResponse('wrong role', 400);
        }
        $userArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($userArray) ? $userArray : array());
        $actUsers = $this->entityManager->getRepository(ActUser::class)->findBy(array('act' => $userArray[0]['actId']));
        foreach ($actUsers as $au) {
            $au->setValidator(0);
            $au->setActValidated(null);
            $au->setValidatedAt(null);
            $au->setMailSent(null);
            $au->setComment(null);
            $this->entityManager->persist($au);
            $this->entityManager->flush();
        }

        foreach ($userArray as $user) {
            $this->actService->setValidator($user);
        }
        return new JsonResponse('validators assigned', 200);
    }

    /**
     * @Route("/synthese-pdf",name="synthese_pdf",options={"expose"=true},methods={"POST"})
     * @return JsonResponse
     */
    public function pdfAction(\Knp\Snappy\Pdf $snappy, Request $request)
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }

        if ($this->checkRole() == true) {
            return new JsonResponse('wrong role', 400);
        }
        $paramArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($paramArray) ? $paramArray : array());

        $currentAct = $paramArray[0];
        $listSignataire = $paramArray[1];
        $listAvocat = $paramArray[2];
        $listDocument = $paramArray[3];
        $listCounsel = array();
        $listSignatory = array();
        $date = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);
        foreach ($listAvocat as $avocat) {
            $contact = $this->entityManager->getRepository(Contact::class)->findOneBy(array('contact' => $avocat['id']));
            $counsel = $contact->getContact();
            $actUser = $this->entityManager->getRepository(ActUser::class)->findOneBy(array('act' => $avocat['actId'], 'user' => $counsel->getId()));
            $avocat['validator'] = $actUser->getValidator();
            array_push($listCounsel, $avocat);
        }
        foreach ($listSignataire as $signataire) {
            $contact = $this->entityManager->getRepository(Contact::class)->findOneBy(array('contact' => $signataire['id']));
            $signatory = $contact->getContact();
            $actUser = $this->entityManager->getRepository(ActUser::class)->findOneBy(array('act' => $signataire['actId'], 'user' => $signatory->getId()));
            $signataire['validator'] = $actUser->getValidator();
            array_push($listSignatory, $signataire);
        }
        $fileName = $date->format('d-m-Y') . '_' . $currentAct['folderNumber'];
        if (file_exists('documents/' . $fileName . '.pdf')) {
            unlink("documents/" . $fileName . ".pdf");
        }
        $html = $this->renderView('act/pdf-synthese.html.twig', array(
            'exportDate' => $date->format('d.m.Y H.i'),
            'currentAct' => $currentAct,
            'listSignataire' => $listSignatory,
            'listAvocat' => $listCounsel,
            'listDocument' => $listDocument,
            'base_dir' => $this->projectDir . '/public' . $request->getBasePath()
        ));
        $snappy->generateFromHtml($html, 'documents/' . $fileName . '.pdf');
        return new JsonResponse($fileName, 200);

    }

    /**
     * @Route("/validate-act",name="validate_act",options={"expose"=true},methods={"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function validatedActAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $userArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($userArray) ? $userArray : array());
        try {
            $result = $this->actService->validateAct($userArray);
        } catch (LoaderError $e) {
        } catch (RuntimeError $e) {
        } catch (SyntaxError $e) {
        }

        return new JsonResponse($result, 200);
    }


    /**
     * @Route("/refuse-act",name="refuse_act",options={"expose"=true},methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function refusedActAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $userArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($userArray) ? $userArray : array());
        $result = $this->actService->refuseAct($userArray);
        return new JsonResponse($result, 200);
    }

    /**
     * @Route("/resend-validation",name="resend",options={"expose"=true},methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function resendValidationAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $userArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($userArray) ? $userArray : array());
        $results = $this->actService->neutralUsers($userArray);
        $users = [];
        $status = '[Relance] Acte à valider';
        $send = $this->mailService;
        $user = $this->getUser();
        if (array_key_exists('unitUser', $userArray) && $userArray['unitUser'] != null) {
            $userArray['id'] = $userArray['unitUser']['id'];
            $userArray['email'] = $userArray['unitUser']['email'];
            $send->sendValidationEmailMessage($user, $userArray, $status);
        } else {
            foreach ($results as $key => $value) {
                $users['id'] = $value->getUser()->getId();
                $users['email'] = $value->getUser()->getEmail();
                $users['actId'] = $userArray['actId'];
                $send->sendValidationEmailMessage($user, $users, $status);
            }
        }
        $act = $this->entityManager->getRepository(Act::class)->find($userArray['actId']);
        $act->setlastResentDate(new DateTime());
        $this->entityManager->persist($act);
        $this->entityManager->flush();

        return new JsonResponse('email sent', 200);
    }


    /**
     * @Route("/resend-signature",name="resend_signature",options={"expose"=true},methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function resendSignatureAction(Request $request)
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $userArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($userArray) ? $userArray : array());
        $results = $this->actService->unsignedUsers($userArray);
        $users = [];
        $status = '[Relance] e-ASSP - Acte à signer électroniquement envoyé par votre avocat ';
        $send = $this->mailService;
        $user = $this->getUser();

        $act = $this->entityManager->getRepository(Act::class)->find($userArray['actId']);
        if ($act->getExpirationDate() !== null ) {

            $now = new DateTime();
            $difference = $act->getExpirationDate()->diff($now)->format('%a');
            $remainingDays = strval($difference);
        }
        else {
            $remainingDays = "21";
        }

        if (array_key_exists('unitUser', $userArray) && $userArray['unitUser'] != null) {
            $userArray['id'] = $userArray['unitUser']['id'];
            $userArray['email'] = $userArray['unitUser']['email'];
            $send->sendSigningEmailMessage($user, $userArray, $status, null, $remainingDays);
        } else {
            foreach ($results as $key => $value) {
                $users['id'] = $value['id'];
                $users['email'] = $value['email'];
                $users['actId'] = $userArray['actId'];
                $send->sendSigningEmailMessage($user, $users, $status, null, $remainingDays);
            }
        }
        $act->setlastResentDate(new DateTime());
        $this->entityManager->persist($act);
        $this->entityManager->flush();

        return new JsonResponse('email sent', 200);

    }


    /**
     * @Route("/sign-notification",name="sign_notification",options={"expose"=true},methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function sendSignatureNotificationAction(Request $request)
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $userArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($userArray) ? $userArray : array());
        $act = $this->entityManager->getRepository(Act::class)->find($userArray['actId']);
        $initiator = $act->getInitiator();
        $status = 'Acte à signer électroniquement';
        $send = $this->mailService;
        $signatory = $this->getUser();
        $send->sendSignNotificationEmail($initiator, $signatory, $status, $act);
        return new JsonResponse('email sent', 200);

    }

    /**
     * @Route("/validate-notification",name="validate_notification",options={"expose"=true},methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function sendValidationNotificationAction(Request $request)
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $userArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($userArray) ? $userArray : array());
        $act = $this->entityManager->getRepository(Act::class)->find($userArray['actId']);
        $initiator = $act->getInitiator();
        $status = 'Acte à signer électroniquement';
        $send = $this->mailService;
        $signatory = $this->entityManager->getRepository(Contact::class)->findOneBy(array('initiator' => $act->getInitiator(), 'contact' => $this->getUser()));
        $send->sendValidatorNotificationToInitiator($initiator, $signatory, $status, $act);
        return new JsonResponse('email sent', 200);
    }

    /**
     * @Route("/refuse-validate-notification",name="refuse_validate_notification",options={"expose"=true},methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function sendRefuseNotifcationToInitiatorAction(Request $request)
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $userArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($userArray) ? $userArray : array());
        $act = $this->entityManager->getRepository(Act::class)->find($userArray['actId']);
        $initiator = $act->getInitiator();
        $status = 'Acte à signer électroniquement';
        $send = $this->mailService;
        $signatory = $this->getUser();
        $send->sendRefuseValidatorNotificationToInitiator($initiator, $signatory, $status, $act);
        return new JsonResponse('email sent', 200);
    }

    /**
     * @Route("/delete",name="delete_act",options={"expose"=true},methods={"DELETE"})
     * @param $request
     * @return JsonResponse
     */
    public function deleteActAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }

        if ($this->checkRole() == true) {
            return new JsonResponse('wrong role', 400);
        }
        $actArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($actArray) ? $actArray : array());
        $this->actService->deleteAct($actArray);
        if ($result = !true) {
            return new JsonResponse('act not deleted', 400);
        }
        return new JsonResponse('act deleted', 200);
    }

    /**
     * @Route("/",name="list",options={"expose"=true},methods={"GET"})
     * @return JsonResponse
     */
    public function getActsAction(): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }

        if ($this->getUser()->getRoles()[0] == "ROLE_COUNSEL") {
            $result = $this->actService->getCouncelActs($this->getUser()->getId());
        } else {
            $result = $this->actService->getActs($this->getUser()->getId());
        }
        return new JsonResponse($result, 200);
    }

    /**
     * @param Request $request
     * @Route("/get-act-users",name="get_act_users",options={"expose"=true},methods={"POST"})
     * @return JsonResponse
     */
    public function findUsersAction(Request $request):JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $actArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($actArray) ? $actArray : array());
        $result = $this->entityManager->getRepository(ActUser::class)->findActUsers($actArray['id']);
        return new JsonResponse($result , 200);
    }

    /**
     * @Route("/",name="new",options={"expose"=true},methods={"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function newActAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }

        if ($this->checkRole() == true) {
            return new JsonResponse('wrong role', 400);
        }
        $actArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($actArray) ? $actArray : array());
        if ($actArray == '') {
            return new JsonResponse('no body found', 404);
        }
        if (!array_key_exists('name', $actArray)) {
            return new JsonResponse('param name required', 404);
        }
        $result = $this->actService->newAct($actArray, $this->getUser());
        if ($result == null) {
            return new JsonResponse('error while creating the act', 400);
        }
        return new JsonResponse($result, 200);
    }

    /**
     * @Route("/{id}",name="get_act_by_id",options={"expose"=true},methods={"GET"})
     * @ParamConverter("Act", options={"id" = "id"})
     * @param Act $act
     * @return JsonResponse
     */
    public function getActByIdAction(Act $act): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $act = $this->actService->getActById($act->getId());
        return new JsonResponse($act, 200);
    }

    /**
     * @Route("/",name="update",options={"expose"=true},methods={"PUT"})
     * @param $request
     * @return JsonResponse
     */
    public function updateActAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }

        if ($this->checkRole() == true) {
            return new JsonResponse('wrong role', 400);
        }
        $actArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($actArray) ? $actArray : array());
        if ($actArray == '') {
            return new JsonResponse('no body found', 404);
        }
        if (!array_key_exists('name', $actArray)) {
            return new JsonResponse('param name required', 404);
        }
        $result = $this->actService->updateAct($actArray, $this->getUser());
        if (sizeof($result) == 0) {
            return new JsonResponse('error while creating the act', 400);
        }
        return new JsonResponse($result, 200);
    }

    /**
     * @Route("/refuse-signature",name="refuse_signature",options={"expose"=true},methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function refusedSignatureActAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }

        $userArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($userArray) ? $userArray : array());
        $result = $this->actService->refuseSignature($userArray);
        $act = $this->entityManager->getRepository(Act::class)->find($userArray['actId']);
        $signatories = $this->entityManager->getRepository(ActUser::class)->findSignatoryWithSigned($act->getId());
        $this->mailService->allSignedNotificationToInitiator($act->getInitiator(), $signatories, 'Acte à signer électroniquement', 'signature refusée');
        return new JsonResponse($result, 200);
    }

    /**
     * @Route("/download-proof",name="download_proof",options={"expose"=true},methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function downloadProofActAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $actArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($actArray) ? $actArray : array());
        $act = $this->entityManager->getRepository(Act::class)->find($actArray[0]);
        $this->orderService->getProofFile($act);

        return new JsonResponse('proof downloaded', 200);
    }

    /**
     * @Route("/get-file-base64",name="get_file_base64",options={"expose"=true},methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getFileBase64Action(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $fileArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($fileArray) ? $fileArray : array());
        if ($_ENV['DEV_MODE']=='true'){
            $result = stripslashes(base64_encode(file_get_contents($this->projectDir . '/src/assets/documents/' . $fileArray[0].'/'.$fileArray[1].'.pdf')));
        }else{
            if (is_dir($this->projectDir.'/src/assets/tmp/' . $fileArray[0]) === false) {
                mkdir($this->projectDir.'/src/assets/tmp/' . $fileArray[0]);
            }
            $exec_commandExport = "export AWS_PROFILE=cloudian";
            $exec_command = " AWS_ACCESS_KEY_ID=" . $_ENV["AWS_ACCESS_KEY_ID"] .
                " AWS_SECRET_ACCESS_KEY=" . $_ENV["AWS_SECRET_ACCESS_KEY"] .
                " aws --endpoint-url=" . $_ENV["ENDPOINT_URL"] . " s3 cp " . $_ENV["ACTS_BUCKET"] ."/" .$fileArray[0] .$_ENV["DOCUMENT"].$fileArray[1].".pdf " .$this->projectDir.'/src/assets/tmp/'.$fileArray[0].'/ '. '--no-verify-ssl';
            shell_exec($exec_commandExport);
            exec($exec_command, $command_output, $return_val);
            if (!$return_val){
                $this->logger->info('merge file for act '.$fileArray[0].' downloaded to server');
            }else {
                $this->logger->error('merge file for act '.$fileArray[0].' did not get downloaded to server');
            }

            $result = stripslashes(base64_encode(file_get_contents($this->projectDir . '/src/assets/tmp/' . $fileArray[0].'/'.$fileArray[1].'.pdf')));
            unlink($this->projectDir.'/src/assets/tmp/' . $fileArray[0].'/'.$fileArray[1].'.pdf');
            if (file_exists($this->projectDir.'/src/assets/documents/' . $fileArray[0].'/'.$fileArray[1].'.pdf')){
                unlink($this->projectDir.'/src/assets/documents/' . $fileArray[0].'/'.$fileArray[1].'.pdf');
            }
        }


        return new JsonResponse($result, 200);
    }
    /**
     * @Route("/get-proof-file-base64",name="get_proof_file_base64",options={"expose"=true},methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getBase64ProofFileAction(Request $request):JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $fileArray = $request->getContent();
        $result = stripslashes(base64_encode(file_get_contents($this->projectDir . '/src/assets' . $fileArray)));
        return new JsonResponse($result, 200);
    }


    /**
     * @Route("/delete-proof-file-base64",name="delete_proof_file_base64",options={"expose"=true},methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteProofFile(Request $request):JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $fileArray = $request->getContent();
        $file = $this->projectDir . '/src/assets' . $fileArray;
        if (file_exists($file)){
            unlink($file);
        }
        return new JsonResponse('true', 200);
    }

    /**
     * @Route("/delete-synthese-file",name="delete_synthese_file",options={"expose"=true},methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteSyntheseFileAction(Request $request):JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $settingArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($settingArray) ? $settingArray : array());
        $file = $this->projectDir . '/public/documents/' . $settingArray[0] . '.pdf';
        unlink($file);
        return new JsonResponse('true', 200);
    }


    private function checkRole()
    {
        $result = false;
        if ($this->getUser()) {
            if ($this->getUser()->getRoles()[0] == 'ROLE_SIGNATORY' or $this->getUser()->getRoles()[0] == 'ROLE_ENTERPRISE') {
                $result = true;
            }
        }
        return $result;
    }



}