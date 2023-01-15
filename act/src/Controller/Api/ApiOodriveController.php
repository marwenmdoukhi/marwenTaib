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
use App\Entity\Archive;
use App\Entity\Contact;
use App\Entity\Document;
use App\Entity\User;
use App\Service\ActService;
use App\Service\MailService;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use DateTime;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


/**
 * Class ApiOodriveController
 * @package App/Controller/Api
 * @Route("/api/oodrive",name="api_oodrive_")
 */
class ApiOodriveController extends AbstractController
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
    /**
     * @var string
     */
    protected $projectDir;
    /**
     * @var OrderService
     */
    private $orderService;


    public function __construct(ActService $actService, MailService $mailService, EntityManagerInterface $entityManager, KernelInterface $kernel, OrderService $orderService)
    {
        $this->actService = $actService;
        $this->mailService = $mailService;
        $this->orderService = $orderService;
        $this->entityManager = $entityManager;
        $this->projectDir = $kernel->getProjectDir();
    }

    /**
     *@Route("/signing-status",name="get_current_signing",options={"expose"=true},methods={"POST"})
     *@param $request
     *@return JsonResponse
     */
    public function getCurrentSigningsAction(Request $request) : JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $result = 0;

        $settingArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($settingArray) ? $settingArray : array());
        if (isset($settingArray['folderNumber'])){
            $result = $this->orderService->signingSignatories($settingArray['folderNumber']);
            return new JsonResponse($result , 200);
        }
        return new JsonResponse($result , 400 );
    }

    /**
     * @Route("/release-signatory",name="release_signatory",options={"expose"=true},methods={"POST"})
     * @param Request $request
     * @param $status
     * @return JsonResponse
     */
    public function updateSignatorySigningAction(Request $request) : JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $result = 0;
        $settingArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($settingArray) ? $settingArray : array());
        if (isset($settingArray[0]['folderNumber'])){
            $result = $this->orderService->updateSigningInProgress($settingArray[0]['folderNumber']);
            return new JsonResponse($result , 200);
        }
        return new JsonResponse($result , 400 );
    }

    /**
     * @Route("/update-signing-slot",name="update_signing_slot",options={"expose"=true},methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function updateSignatorySigningSlot(Request $request) :JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $settingArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($settingArray) ? $settingArray : array());
        $result = 0;
        if (isset($settingArray['folderNumber'])){
            $result = $this->orderService->updateSigningInProgressTimeStamp($settingArray['folderNumber']);
            return new JsonResponse($result , 200);
        }
        return new JsonResponse($result , 400 );

    }


    /**
     * @Route("/create-order",name="create_order",options={"expose"=true},methods={"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function createOrderAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        ini_set('gd.jpeg_ignore_warning', 1);
        $result = 0;
        $user = $this->getUser();
        $settingArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($settingArray) ? $settingArray : array());

        if (isset($settingArray['folderName']) and isset($settingArray['image'])) {
            $act = $this->entityManager->getRepository(Act::class)->findOneBy(array('folderNumber' => $settingArray['folderName']));
            $contact = $this->entityManager->getRepository(Contact::class)->findOneBy(array('contact' => $user, 'initiator' => $act->getInitiator()));
            $settingArray['image'] = str_replace("data:image/png;base64,", "", $settingArray['image']);
            $result = $this->orderService->createOrder($contact, $settingArray['folderName'], $settingArray['image'] , $user);
            return new JsonResponse($result, 200);
        }

        return new JsonResponse($result, 400);
    }

    /**
     * @Route("/sign-otp",name="sign_otp",options={"expose"=true},methods={"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function SignOrderWithOtpAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        set_time_limit(0);
        $result = '';
        $date = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);
        $day = $date->format('d');
        $month = $date->format('m');
        $year = $date->format('Y');
        $user = $this->getUser();
        $settingArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($settingArray) ? $settingArray : array());

        if (isset($settingArray['orderId']) and isset($settingArray['otp']) and isset($settingArray['folderName'])) {
            $result = $this->orderService->SignOrderWithOtp($settingArray['orderId'], $settingArray['otp'], $settingArray['folderName']);
            if ($result[0] == 'done signing') {
                $act = $this->entityManager->getRepository(Act::class)->findOneBy(array('folderNumber' => $settingArray['folderName']));
                $actUser = $this->entityManager->getRepository(ActUser::class)->findOneBy(array('user' => $user->getId(), 'act' => $act->getId()));
                $actUser->setSignedAt($date);
                $this->entityManager->persist($actUser);
                $archive = new Archive();
                $archive->setAct($actUser->getAct());
                $archive->setUser($actUser->getUser());
                $archive->setActionDate($date);
                $archive->setActor($actUser->getUser()->getLastName() . ' ' .strtoupper($actUser->getUser()->getName()));
                $archive->setAction('Action: Signature de l\'acte - Accepté');
                $this->entityManager->persist($archive);
                $this->entityManager->flush();
                $userActs = $this->entityManager->getRepository(ActUser::class)->findSignatoryWithNotSigned($act->getId());
                if (sizeof($userActs) == 0) {
                    $act->setStatus('Signee');
                    $act->setSigningDate($date);
                    $status = 'Acte à signer électroniquement';
                    $signatories = $this->entityManager->getRepository(ActUser::class)->findSignatoryWithSigned($act->getId());
                    $this->mailService->allSignedNotificationToInitiator($act->getInitiator(), $signatories, $status, 'signé');
                    $documents = $act->getDocuments();
                    foreach ($documents as $document) {
                        $file = $this->projectDir . '/src/assets/documents/' . $act->getId() . '/' . $document->getName() . '.pdf';
                        $act->removeDocument($document);
                        $this->entityManager->remove($document);
                        if (file_exists($file)){
                            unlink($file);
                        }
                        $exec_commandExport = "export AWS_PROFILE=cloudian";
                        $exec_command = " AWS_ACCESS_KEY_ID=" . $_ENV["AWS_ACCESS_KEY_ID"] .
                            " AWS_SECRET_ACCESS_KEY=" . $_ENV["AWS_SECRET_ACCESS_KEY"] .
                            " aws --endpoint-url=" . $_ENV["ENDPOINT_URL"] .
                            " s3 rm " . $_ENV["ACTS_BUCKET"] .'/'.$act->getId().$_ENV["DOCUMENT"].$document->getName() . '.pdf'. " --no-verify-ssl";
                        shell_exec($exec_commandExport);
                        exec($exec_command, $command_output, $return_val);
                    }

                    //rmdir($this->projectDir.'/src/assets/documents/'.$act->getId().'/');
                    $f = filesize($this->projectDir . '/src/assets/documents/' . $act->getFolderNumber() . 'ForSigning.pdf');
                    $mergedFile = new Document();
                    $mergedFile->setName($act->getName() . '-signé-' . $year . $month . $day);
                    $mergedFile->setStatus('Created');
                    $mergedFile->setSize($f);
                    $mergedFile->setType('pdf');
                    $mergedFile->setConvertedType('pdfa2b');
                    $mergedFile->setPosition(0);
                    $this->entityManager->persist($mergedFile);
                    $act->addDocument($mergedFile);
                    $this->entityManager->persist($act);
                    $this->entityManager->flush();
                    $this->orderService->proofFile($act);
                    $file = $this->projectDir . '/src/assets/documents/' . $act->getFolderNumber() . 'ForSigning.pdf';
                    unlink($file);
                    $subject = 'e-ASSP - Téléchargez votre acte signé électroniquement ';
                    $users = $this->entityManager->getRepository(ActUser::class)->findUsers($act);
                    $initiator = $act->getInitiator();
                    $actId = $act->getId();
                    foreach ($users as $u) {
                        try {
                            $this->mailService->downloadFileEmailMessage($initiator, $u, $actId, $subject);
                        } catch (LoaderError $e) {
                        } catch (RuntimeError $e) {
                        } catch (SyntaxError $e) {
                        }
                    }
                    $exec_commandExport = "export AWS_PROFILE=cloudian";
                    $exec_command = " AWS_ACCESS_KEY_ID=" . $_ENV["AWS_ACCESS_KEY_ID"] .
                        " AWS_SECRET_ACCESS_KEY=" . $_ENV["AWS_SECRET_ACCESS_KEY"] .
                        " aws --endpoint-url=" . $_ENV["ENDPOINT_URL"] .
                        " s3 rm --recursive" . $_ENV["ACTS_BUCKET"] .'/'.$act->getId().'/'. " --no-verify-ssl";
                    shell_exec($exec_commandExport);
                    exec($exec_command, $command_output, $return_val);
                }
                return new JsonResponse('file signed created', 200);
            }
        }

        return new JsonResponse($result, 200);

    }



    /**
     * @Route("/get-signed-file",name="get_signed_file",options={"expose"=true},methods={"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function getSignedFileAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $settingArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($settingArray) ? $settingArray : array());
        $act = $this->entityManager->getRepository(Act::class)->find($settingArray);
        $result =  $this->orderService->getSignedOrder($act->getOrderRequestId(), $act->getFolderNumber());
        return new JsonResponse($result, 200);
    }

    /**
     * @Route("/delete-signed-file",name="delete_signed_file",options={"expose"=true},methods={"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function deleteSignedFileAction(Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $settingArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($settingArray) ? $settingArray : array());
        $act = $this->entityManager->getRepository(Act::class)->find($settingArray[0]['id']);
        $file = $this->projectDir . '/public/documents/' . $act->getFolderNumber().$settingArray[1] . 'ForSigning.pdf';
        unlink($file);
        return new JsonResponse('true', 200);

    }
    /**
     * @Route("/generate_otp",name="generate_otp",options={"expose"=true},methods={"POST"})
     * @param $request
     * @return JsonResponse
     */
    public function generateOtp(Request $request) : JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $settingArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($settingArray) ? $settingArray : array());
        $codeCountry = str_replace('+', '%2B', $settingArray[1]['codeCountry']);
        $phone = $settingArray[1]['phoneNumber'];
        $this->orderService->generateOtpCode($settingArray[0], $codeCountry.$phone);
        return new JsonResponse('true',200);
    }

    /**
     * @Route("/validate-otp",name="validate_otp",options={"expose"=true},methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function validateOtpAction(Request $request) : JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse('Operation not permitted', 400);
        }
        $settingArray = json_decode($request->getContent(), true);
        $request->request->replace(is_array($settingArray) ? $settingArray : array());
        $result =  $this->orderService->validateOtp($settingArray[0], $settingArray[1]);
        $status = 200;
        if ($result == false) {
            $status = 500;
        }
        return new JsonResponse($result , $status);
    }



}