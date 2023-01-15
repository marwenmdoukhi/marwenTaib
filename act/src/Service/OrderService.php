<?php
/**
 * Created by PhpStorm.
 * User: Helmi
 * Date: 27/01/2020
 * Time: 14:19
 */

namespace App\Service;

use App\Entity\Act;
use App\Entity\ActUser;
use App\Entity\Proof;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\DateTime;
use Twig\Environment;

/**
 * Class OrderService
 * @package App\Service
 */
class OrderService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    private $security;
    /**
     * @var \Knp\Snappy\Pdf
     */
    private $snappy;
    /**
     * @var Environment
     */
    private $templating;
    protected $projectDir;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, Security $security, \Knp\Snappy\Pdf $snappy, Environment $templating, KernelInterface $kernel, LoggerInterface $oodriveLogger)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->snappy = $snappy;
        $this->templating = $templating;
        $this->projectDir = $kernel->getProjectDir();
        $this->logger = $oodriveLogger;
    }

    /**
     * @param $user
     * @param $folderNumber
     * @param $image
     * @return mixed
     */
    public function createOrder($user, $folderNumber, $image , $contactUser)
    {
        $zip = "";
        $date = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);
        $url = $_ENV['CREATE_ORDER_URL'];
        $data = array(
            "holder" => [
                "firstname" => $user->getName(),
                "lastname" => $user->getLastName(),
                "email" => $user->getEmail(),
                "mobile" => str_replace("+", "", $user->getCodeCountry()).$user->getPhoneNumber(),
                "country" => "FR",
                "idNumber" => "123456",
                "idType" => "IDCARD",
            ],
            "proofFolder" => [
                "zipFiles" => ""
            ],
            "clientIdentifier" => $user->getEmail(),
            "otpContact" => $user->getEmail(),
            "enableOtp" => true,
            "enableEmail" => false,
            "enableSharing" => false,
        );
        $data_string = json_encode($data);

        $ch = curl_init();

        $options = array(
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL => $url,
            CURLOPT_SSLCERT => $_ENV['OODRIVE_CRT'],
            CURLOPT_SSLKEY => $_ENV['OODRIVE_KEY'],
            CURLOPT_POSTFIELDS => $data_string,

        );
        curl_setopt_array($ch, $options);
        $output = curl_exec($ch);
        $result = json_decode($output, true);


        $act = $this->entityManager->getRepository(Act::class)->findOneBy(array('folderNumber' => $folderNumber));
        $actUsers = $this->entityManager->getRepository(ActUser::class)->findBy(array('act'=>$act));
        $actUser = $this->entityManager->getRepository(ActUser::class)->findOneBy(array('act' => $act, 'user' => $this->security->getUser()));
        if (isset($result['orderRequestId'])) {
            $this->logger->info('Create order for act:' . $folderNumber . ' cnbId' . $act->getInitiator()->getCnbId() . ' signatory' . $user->getName() . ' ' . $user->getLastName() . ' orderId ' . $result['orderRequestId']);
            $actUser->setOrderId($result['orderRequestId']);
            $act->setOrderRequestId($result['orderRequestId']);
            $this->entityManager->persist($act);
            $this->entityManager->persist($actUser);
            $this->entityManager->flush();
            $r = $this->createDocumentInOrder($result['orderRequestId'], $folderNumber, $image, $user , $contactUser , $act);
            if (isset($r[0]['orderRequestId'])) {
                $this->lunchOtp($r[0]['orderRequestId']);
            }
            return $result['orderRequestId'];
        } else {
            $actUser->setSigningInProgress(false);
            $this->entityManager->persist($actUser);
            $this->entityManager->flush();
            $this->logger->error('Error create order for act:' . $folderNumber . ' cnbId' . $act->getInitiator()->getCnbId() . ' signatory' . $user->getName() . ' ' . $user->getLastName() . ' error message ' . $result['errorMsg']);
        }
        return 'error order creation';
    }

    function countPages($path , $act) {
        $pdftext = file_get_contents($path);
        $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);
        $act->setActPdfPagesNumber($num);
        $this->entityManager->persist($act);
        $this->entityManager->flush();
        return $num;
    }


    /**
     * @param $orderId
     * @param $folderNumber
     * @param $image
     * @param $user
     * @param $contactUser
     * @return mixed
     */
    public function createDocumentInOrder($orderId, $folderNumber, $image, $user , $contactUser , $act)
    {
        $archive = false;
        $date = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);
        $act = $this->entityManager->getRepository(Act::class)->findOneBy(array('folderNumber' => $folderNumber));
        $proof = new Proof();
        $proof->setContact($user);
        $proof->setDate($date);
        $proof->setAct($act);
        $proof->setEvent('Signature du document');
        $proof->setIpAddress(isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '');
        $contactUser->setIpaddress(isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '');
        $this->entityManager->persist($contactUser);
        $this->entityManager->persist($proof);
        $this->entityManager->flush();
        $ipAddress = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '';
        $ip  = strstr($ipAddress, ',', true);
        $y = 480;
        if (!file_exists($this->projectDir . '/src/assets/documents/'  . 'ForSigning.pdf')){
            $exec_commandExport = "export AWS_PROFILE=cloudian";
            $exec_command_Path_test = " AWS_ACCESS_KEY_ID=" . $_ENV["AWS_ACCESS_KEY_ID"] .
                " AWS_SECRET_ACCESS_KEY=" . $_ENV["AWS_SECRET_ACCESS_KEY"] .
                " aws --endpoint-url=" . $_ENV["ENDPOINT_URL"] .
                " s3 cp ". $_ENV["ACTS_BUCKET"] .'/'.$act->getId().'/merged/'. $act->getFolderNumber() . '.pdf ' . $this->projectDir.'/src/assets/documents/'   . " --no-verify-ssl";
            shell_exec($exec_commandExport);
            exec($exec_command_Path_test, $command_output, $return_output);
            if (!$return_output){
                $this->logger->info('forsigning file for act '.$act->getId().' merged and uploaded to s3');
            }else{
                $this->logger->error('forsigning file for act '.$act->getId(). ' did not upload to s3');
            }
        }
        $update = new \DateTime('2021-11-10 12:45:00');
        if ($act->getRequestDate() < $update){
            $page = 1;
            $this->logger->info('this act is pre update '.$act->getRequestDate().' '.$update);
        }else{
            if ($act->getActPdfPagesNumber() == null){
                $pdf = $this->projectDir.'/src/assets/documents/' . $act->getFolderNumber() . '.pdf';
                $totalPages = $this->countPages($pdf , $act);
                $this->logger->info('pdf page number for act:' . $folderNumber . 'is '.$totalPages);
            }else{
                $totalPages = $act->getActPdfPagesNumber();
                $this->logger->info('pdf page number for act:' . $folderNumber . 'is '.$totalPages);
            }
            $page = $totalPages+1;
        }
        $act = $this->entityManager->getRepository(Act::class)->findOneBy(array('folderNumber' => $folderNumber));
        $userAct = $this->entityManager->getRepository(ActUser::class)->findBy(array('act' => $act->getId(), 'signedAt' => null));
        if (sizeof($userAct) == 1) {
            $archive = $_ENV['ENABLE_ARCHIVE'];
        }

        $signatories = $this->entityManager->getRepository(User::class)->findSignatoryByAct($act,$act->getInitiator());
        foreach ($signatories as $key => $val) {
            if ($val['id'] == $this->security->getUser()->getId()) {
                if ($key <= 4) {
                    $page = $page + (intdiv($key, 5));
                    $y = 487 - ($key * 102);
                    //if localhost use y =   495/ 502
                } elseif ($key  == 5) {
                    $page = $page + (intdiv($key+1, 6));
                    $y = 712;
                }
                else {
                    $page = $page + (intdiv($key+1, 6));
                    $key = (($key % 6) + 1) % 6;
                    $y = 712 - ($key * 128);
                }
            }

        }
        if (!file_exists($this->projectDir . '/src/assets/documents/' . $act->getFolderNumber() . 'ForSigning.pdf')){
            $exec_commandExport = "export AWS_PROFILE=cloudian";
            $exec_command_Path_test = " AWS_ACCESS_KEY_ID=" . $_ENV["AWS_ACCESS_KEY_ID"] .
                " AWS_SECRET_ACCESS_KEY=" . $_ENV["AWS_SECRET_ACCESS_KEY"] .
                " aws --endpoint-url=" . $_ENV["ENDPOINT_URL"] .
                " s3 cp ". $_ENV["ACTS_BUCKET"] .'/'.$act->getId().'/merged/'. $act->getFolderNumber() . 'ForSigning.pdf ' . $this->projectDir.'/src/assets/documents/'   . " --no-verify-ssl";
            shell_exec($exec_commandExport);
            exec($exec_command_Path_test, $command_output, $return_output);
            if (!$return_output){
                $this->logger->info('forsigning file for act '.$act->getId().' merged and uploaded to s3');
            }else{
                $this->logger->error('forsigning file for act '.$act->getId(). ' did not upload to s3');
            }
        }
        $url = $_ENV['CREATE_DOCUMENT_URL'] . $orderId;
        $data = array(array(
            "signatureOptions" => [
                "signatureType" => "PAdES_BASELINE_LTA",
                "digestAlgorithmName" => "SHA256",
                "signaturePackagingType" => "ENVELOPED",
                "documentType" => "INLINE"
            ],
            "pdfSignatureOptions" => [
                "signatureTextColor" => 8998,
                "signatureTextFontSize" => 12,
                "fontFamily" => "Courier",
                "fontStyle" => "Bold",
                "signatureImageContent" => $image,
                "signatureText" => $date->format('d-m-Y H:i'),
                "signaturePosX" => 300,
                "signaturePosY" => $y,
                "signaturePage" => $page
            ],
            "enableArchive" => $archive,
            "archiverNames" => [$_ENV['ARCHIVE_NAME']],
            "toSignContent" => $encode = base64_encode(file_get_contents($this->projectDir . '/src/assets/documents/' . $folderNumber . 'ForSigning.pdf'))
        ));
        $data_string = json_encode($data);

        $ch = curl_init();

        $options = array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)',
            CURLOPT_URL => $url,
            CURLOPT_SSLCERT => $_ENV['OODRIVE_CRT'],
            CURLOPT_SSLKEY => $_ENV['OODRIVE_KEY']
        );

        curl_setopt_array($ch, $options);
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        if ($result[0]['orderRequestId']) {
            $this->logger->info('Create document for act:' . $folderNumber . ' cnbId' . $act->getInitiator()->getCnbId() . ' signatory' . $user->getName() . ' ' . $user->getLastName() . ' orderId ' . $result[0]['orderRequestId'] . ' signature y ' . $y . ' page ' . $page);

        } else {
            $this->logger->error('Error Create document for act:' . $folderNumber . ' cnbId' . $act->getInitiator()->getCnbId() . ' signatory' . $user->getName() . ' ' . $user->getLastName() . ' orderId ' . $result['orderRequestId'] . ' signature y ' . $y . ' page ' . $page . ' error ' . $result['errorMsg']);
        }
        return $result;
    }

    /**
     * @param $orderId
     * @return mixed
     */
    public function lunchOtp($orderId)
    {
        $url = $_ENV['LUNCH_OTP_URL'] . $orderId;

        $ch = curl_init();

        $options = array(
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL => $url,
            CURLOPT_SSLCERT => $_ENV['OODRIVE_CRT'],
            CURLOPT_SSLKEY => $_ENV['OODRIVE_KEY']
        );

        curl_setopt_array($ch, $options);
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        if (!isset($result['errorMsg'])) {
            $this->logger->info('launch otp: ' . $orderId);

        } else {
            $this->logger->error('error launch otp: ' . $orderId . ' error message ' . $result['errorMsg']);
        }
        return $result;
    }

    /**
     * @param $orderId
     * @param $otp
     * @param $folderName
     * @return mixed
     */
    public
    function SignOrderWithOtp($orderId, $otp, $folderName)
    {
        $url = $_ENV['SIGN_ORDER_URL'] . $orderId . "&otp=" . $otp . "&mode=SYNC";

        $ch = curl_init();

        $options = array(
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL => $url,
            CURLOPT_SSLCERT => $_ENV['OODRIVE_CRT'],
            CURLOPT_SSLKEY => $_ENV['OODRIVE_KEY']
        );

        curl_setopt_array($ch, $options);
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        $act = $this->entityManager->getRepository(Act::class)->findOneBy(array('folderNumber' => $folderName));
        $actUser = $this->entityManager->getRepository(ActUser::class)->findOneBy(array('act' => $act, 'user' => $this->security->getUser()));
        if (isset($result[0]['signatureRequestId'])) {
            $this->logger->info('Signing order for act:' . $folderName . ' cnbId' . $act->getInitiator()->getCnbId() . ' signatory' . $this->security->getUser()->getName() . ' ' . $this->security->getUser()->getLastName().' signatureId'.$result[0]['signatureRequestId']);
            $actUser->setSignatureId($result[0]['signatureRequestId']);
            $actUser->setSigningInProgress(false);
            $this->entityManager->persist($actUser);
            $this->entityManager->flush();
            return $this->getSignedOrder($result[0]['signatureRequestId'], $folderName);
        } else {
            $actUser->setSigningInProgress(false);
            $this->entityManager->persist($actUser);
            $this->entityManager->flush();
            $this->logger->error('Error signing order for act:' . $folderName . ' cnbId' . $act->getInitiator()->getCnbId() . ' signatory' . $this->security->getUser()->getName() . ' ' . $this->security->getUser()->getLastName() . ' error message ' . $result['errorMsg']);
            return $result['errorMsg'];
        }
    }

    /**
     * @param $signatureId
     * @param $folderName
     * @return mixed
     */
    public function getSignedOrder($signatureId, $folderName)
    {
        $url = $_ENV['GET_SIGNED_DOCUMENT_URL'] . $signatureId;

        $ch = curl_init();

        $options = array(
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL => $url,
            CURLOPT_SSLCERT => $_ENV['OODRIVE_CRT'],
            CURLOPT_SSLKEY => $_ENV['OODRIVE_KEY']
        );

        curl_setopt_array($ch, $options);
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        $act = $this->entityManager->getRepository(Act::class)->findOneBy(array('folderNumber' => $folderName));
        if (isset($result['errorMsg'])) {
            $this->logger->error('Error get signed file for act:' . $folderName . ' cnbId' . $act->getInitiator()->getCnbId() . ' signatureId' . $signatureId . ' error message ' . $result['errorMsg']);
        } else {
            $this->logger->info('Get signed file for act:' . $folderName . ' cnbId' . $act->getInitiator()->getCnbId() . ' signatureId' . $signatureId);
        }
        $act->setOrderRequestId($signatureId);
        $this->entityManager->persist($act);
        $this->entityManager->flush();
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        $n = 10;
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        if ($act->getStatus() == 'Signee'){
            file_put_contents('documents/' . $folderName .$randomString. 'ForSigning.pdf', base64_decode($result['signedContent']));
        }else{
            file_put_contents($this->projectDir . '/src/assets/documents/' . $folderName . 'ForSigning.pdf', base64_decode($result['signedContent']));
        }
        if ($_ENV['DEV_MODE']=='false') {
            if (file_exists($this->projectDir . '/src/assets/documents/' . $folderName . '.pdf')) {
                unlink($this->projectDir . '/src/assets/documents/' . $folderName . '.pdf');
            }
            if (file_exists($this->projectDir . '/src/assets/documents/frontPage' . $folderName . '.pdf')) {
                unlink($this->projectDir . '/src/assets/documents/frontPage' . $folderName . '.pdf');
            }
        }
//        if (file_exists($this->projectDir . '/src/assets/documents/' . $folderName . '.pdf')) {
//            unlink($this->projectDir . '/src/assets/documents/' . $folderName . '.pdf');
//        }
//        if (file_exists($this->projectDir . '/src/assets/documents/frontPage' . $folderName . '.pdf')) {
//            unlink($this->projectDir . '/src/assets/documents/frontPage' . $folderName . '.pdf');
//        }
        $arrayResult[0] = 'done signing';
        $arrayResult[1] = $randomString;
        return $arrayResult;
    }

    public function proofFile($act)
    {
        $proof = $this->entityManager->getRepository(Proof::class)->findBy(array('act' => $act), array('date' => 'ASC'));

        $html = $this->templating->render('act/proofFile.html.twig', array(
            'proof' => $proof,
            'base_dir' => $this->projectDir . '/public'
        ));
        if (file_exists($this->projectDir . '/src/assets/tmp/' . $act->getFolderNumber() . 'Proof.pdf')) {
            chmod($this->projectDir . '/src/assets/tmp/' . $act->getFolderNumber() . 'Proof.pdf', 777);
            unlink($this->projectDir . '/src/assets/tmp/' . $act->getFolderNumber() . 'Proof.pdf');
        }
        try {
            $this->snappy->generateFromHtml($html, $this->projectDir . '/src/assets/tmp/' . $act->getFolderNumber() . 'Proof.pdf');
        } catch (\RuntimeException $e) {
            if (!file_exists($this->projectDir . '/src/assets/tmp/' . $act->getFolderNumber() . 'Proof.pdf')) {
                dump('exception');
                return false;
            }
        }
        chmod($this->projectDir . '/src/assets/tmp/' . $act->getFolderNumber() . 'Proof.pdf', 777);
        if ($_ENV['DEV_MODE']=='true') {
            return true;
        } else {
            $exec_commandExport = "export AWS_PROFILE=cloudian";
            $exec_command = " AWS_ACCESS_KEY_ID=" . $_ENV["AWS_ACCESS_KEY_ID"] . " AWS_SECRET_ACCESS_KEY=" . $_ENV["AWS_SECRET_ACCESS_KEY"] . " aws --endpoint-url=" . $_ENV["ENDPOINT_URL"] . " s3 cp " . $this->projectDir . '/src/assets/tmp/' . $act->getFolderNumber() . 'Proof.pdf ' . $_ENV["BUCKET"] . " --no-verify-ssl";
        }
        shell_exec($exec_commandExport);
        exec($exec_command, $command_output, $return_val);
        if (!$return_val) {
            $this->logger->info('proof file for act '.$act->getId().' merged and uploaded to s3');
            return true;
        }
        return false;
    }

    public function getProofFile($act)
    {
        if ($_ENV['DEV_MODE']=='true') {

        } else {
            $exec_commandExport = "export AWS_PROFILE=cloudian";
            $exec_command = " AWS_ACCESS_KEY_ID=" . $_ENV["AWS_ACCESS_KEY_ID"] . " AWS_SECRET_ACCESS_KEY=" . $_ENV["AWS_SECRET_ACCESS_KEY"] . " aws --endpoint-url=" . $_ENV["ENDPOINT_URL"] . " s3 cp " . $_ENV["BUCKET"] ."/" .$act->getFolderNumber() . 'Proof.pdf ' .$this->projectDir.'/src/assets/tmp/ '. '--no-verify-ssl';
            shell_exec($exec_commandExport);
            exec($exec_command, $command_output, $return_val);
        }

        return 'proof folder downloaded';
    }

    public function signingSignatories($folderNumber)
    {
        $act = $this->entityManager->getRepository(Act::class)->findOneBy(array('folderNumber' => $folderNumber));
        $actUsers = $this->entityManager->getRepository(ActUser::class)->findBy(array('act'=>$act));
        $loggedUser = null;
        $isUserLoggedFound = false;

        foreach ($actUsers as $user){
            if (!$isUserLoggedFound  and $user->getUser() == $this->security->getUser()){
                $loggedUser = $user;
                $isUserLoggedFound = true;
            }
            if ($user->getSigningInProgress() == true and $user->getUser() != $this->security->getUser()){
                $date = strtotime("now");
                $signingSlotDate = strtotime($user->getSigningTimeStamp()->format('Y-m-d h:i:s'));
                $milliSeconds = $date - $signingSlotDate ;
                $minutes = date("i" , $milliSeconds);
                if ($minutes > 15){
                    $user->setSigningInProgress(null);
                    $user->setSigningTimeStamp(null);
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                    return false;
                }else{
                    return "wrn-0200";
                }

            }
        }
        if ($isUserLoggedFound){
            $loggedUser->setSigningInProgress(true);
            $loggedUser->setSigningTimeStamp(new \DateTime());
            $this->entityManager->persist($loggedUser);
            $this->entityManager->flush();
            return true;
        }else {
            return false;
        }
    }

    public function updateSigningInProgress($folderNumber)
    {
        $act = $this->entityManager->getRepository(Act::class)->findOneBy(array('folderNumber' => $folderNumber));
        $actUser = $this->entityManager->getRepository(ActUser::class)->findOneBy(array('act' => $act, 'user' => $this->security->getUser()));
        $actUser->setSigningInProgress(null);
        $actUser->setSigningTimeStamp(null);
        $this->entityManager->persist($actUser);
        $this->entityManager->flush();
        return true;
    }

    public function updateSigningInProgressTimeStamp($folderNumber)
    {
        $act = $this->entityManager->getRepository(Act::class)->findOneBy(array('folderNumber' => $folderNumber));
        $actUser = $this->entityManager->getRepository(ActUser::class)->findOneBy(array('act' => $act, 'signingInProgress' => true));
        $date = strtotime("now");
        if ($actUser){
            $signingSlotDate = strtotime($actUser->getSigningTimeStamp()->format('Y-m-d h:i:s'));
            $milliSeconds = $date - $signingSlotDate ;
            $minutes = date("i" , $milliSeconds);
            if ($minutes > 30){
                $actUser->setSigningInProgress(null);
                $actUser->setSigningTimeStamp(null);
                $this->entityManager->persist($actUser);
                $this->entityManager->flush();
            }
        }
        return true;
    }

    public function generateOtpCode($folderNumber , $phoneNumber)
    {
        $act = $this->entityManager->getRepository(Act::class)->findOneBy(array('id' => $folderNumber));
        $actUser = $this->entityManager->getRepository(ActUser::class)->findOneBy(array('act' => $act, 'user' => $this->security->getUser()));
        $otp = random_int(100000, 999999);
        $actUser->setOtpCode($otp);
        $this->entityManager->persist($actUser);
        $this->entityManager->flush();
        $otpmessage ="Votre%20code%20%E0%20usage%20unique%20OTP%20est%20le%20".$otp."%2E%20Il%20expire%20dans%2015%20minutes.%20Ne%20le%20partagez%20avec%20personne%2E";
        $url = $_ENV['SEND_OTP'].$_ENV['MESSAGE_UNITAIRE'].$otpmessage.$_ENV['OTP_FROM'].$phoneNumber.$_ENV['OTP_END'];
        $ch = curl_init();
        $options = array(
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL => $url,
        );

        curl_setopt_array($ch, $options);
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        $this->logger->info('sending sms to '.$phoneNumber.' for act '.$folderNumber);
        $this->logger->info($url);
        $this->logger->info($output);
        return $result;
    }

    public function validateOtp($folderNumber, $otp)
    {
        $act = $this->entityManager->getRepository(Act::class)->findOneBy(array('id' => $folderNumber));
        $actUser = $this->entityManager->getRepository(ActUser::class)->findOneBy(array('act' => $act, 'user' => $this->security->getUser()));
        $codeOtp =  $actUser->getOtpCode();
        if ($codeOtp == $otp){
            $result = true;
        }else{
            $this->logger->error('erreur for '.$folderNumber.' and for opt '.$otp);
            $result = false;
        }
        return $result;
    }



}