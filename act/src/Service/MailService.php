<?php
/**
 * Created by PhpStorm.
 * User: Helmi
 * Date: 09/03/2020
 * Time: 09:59
 */

namespace App\Service;

use App\Entity\Act;
use App\Entity\Contact;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;


class MailService
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var EngineInterface|Environment
     */
    private $templating;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var Security
     */
    private $security;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(\Swift_Mailer $mailer, Environment $templating, EntityManagerInterface $entityManager, Security $security, LoggerInterface $mandrillLogger)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->logger = $mandrillLogger;
    }

    /**
     * @param $user
     * @param $u
     * @param $subject
     * @param null $newDate
     */
    public function sendValidationEmailMessage($user, $u, $subject,$newDate=null)
    {
        if($newDate==null){
            $date = new DateTime();
            $timestamp = $date->getTimestamp();
        }else{
            $timestamp = $newDate->getTimestamp();
        }
        $contact = $this->entityManager->getRepository(Contact::class)->findOneBy(array('contact'=>$u['id'],'initiator'=>$this->security->getUser()));
        $userRecepient = $this->entityManager->getRepository(User::class)->find($u['id']);
        if ($userRecepient == null) {
            $userRecepient= $this->entityManager->getRepository(Contact::class)->findOneBy(array('initiator'=>$user,'contact' => $u['id']));
            $id = $userRecepient->getContact()->getId();
        } else {
            $id = $userRecepient->getId();
        }
        $data = array(
            "key" => "6vp_r7CzdJCaedGUU0Jecw",
            "template_name" => "validation",
            "template_content" => array(
                array(
                    'name' => 'expiditeur',
                    'content' =>$user->getLastName() . ' '.$user->getName() . ' '
                ),
                array(
                    'name' => 'email',
                    'content' => $user->getEmail()
                ),
            ),
            "message" => [
                "global_merge_vars" => array(
                    array(
                        "name" => "LINK",
                        "content" => $_ENV['HOST'] . 'validation?id=' . $id . '&act=' . $u['actId'] . '&timestamp=' . $timestamp.'&version='.$contact->getVersion()
                    )
                ),
                "html" => "",
                "subject" => $subject,
                "from_email" => $_ENV['SENDER'],
                "from_name" => "e-Actes sous signature privée",
                "to" => [
                    [
                        "email" => $u['email'],
                        "name" => "",
                        "type" => "to"
                    ]

                ],
                "track_opens" => "true",
                "track_clicks" => "true",
                "tags" => [
                    "assp",
                    $_ENV['ENVIRONMENT']
                ],
                "subaccount" => "assp"
            ],
            "async" => false,
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
            CURLOPT_URL => 'https://mandrillapp.com/api/1.0/messages/send-template.json',
            CURLOPT_POSTFIELDS => $data_string,

        );
        $act = $this->entityManager->getRepository(Act::class)->find($u['actId']);
        if (strpos($u['email'], '@cnb') == false) {
            curl_setopt_array($ch, $options);
            $output = curl_exec($ch);
            $result = json_decode($output, true);
            if (isset($result['error'])) {
                $this->logger->error($result['error'] . ' ' . $result['message'] . ' Validation email from ' . $user->getCnbId() . ' to ' . $u['email'] . ' acte :' . $act->getFolderNUmber() . ' sujet : ' . $subject);
            } else {
                $this->logger->info('Validation email from ' . $user->getCnbId() . ' to ' . $u['email'] . ' acte :' . $act->getFolderNUmber() . ' sujet : ' . $subject);
            }
        } else {
            $this->logger->error('Validation email contains @cnb from ' . $user->getCnbId() . ' to ' . $u['email'] . ' acte :' . $act->getFolderNUmber() . ' sujet : ' . $subject);
        }

        return true;
    }

    /**
     * @param $user
     * @param $u
     * @param $subject
     * @param null $newDate
     */
    public function sendSigningEmailMessage($user, $u, $subject,$newDate=null, $remainingDays="21")
    {
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        if($newDate==null){
            $date = new DateTime();
            $timestamp = $date->getTimestamp();
        }else{
            $timestamp = $newDate->getTimestamp();
        }
        $act=$this->entityManager->getRepository(Act::class)->find($u['actId']);
        $contact = $this->entityManager->getRepository(Contact::class)->findOneBy(array('contact'=>$u['id'],'initiator'=>$act->getInitiator()));
        $userRecepient = $this->entityManager->getRepository(User::class)->find($u['id']);
        key
        template_name
        template_content
         message ==[global_merge_vars html subject  from_email from_name  to[] track_opens track_clicks  "tags" => [
        "assp",
        $_ENV['ENVIRONMENT']
    ],
               "subaccount" => "assp"
            ],
            "async" => false,


        $data = array(
            "key" => "6vp_r7CzdJCaedGUU0Jecw",
            "template_name" => "signing",
            "template_content" => array(
                array(
                    'name' => 'expiditeur',
                    'content' => $user->getLastName() . ' '.$user->getName() . ' '
                ),
                array(
                    'name' => 'email',
                    'content' => $user->getEmail()
                ),
                array(
                    'name' => 'remainingDays',
                    'content' => $remainingDays
                ),
            ),
            "message" => [
                "global_merge_vars" => array(
                    array(
                        "name" => "LINK",
                        "content" => $_ENV['HOST'] . 'signing?id=' . $userRecepient->getId() . '&act=' . $u['actId'] . '&timestamp=' . $timestamp.'&version='.$contact->getVersion()
                    )
                ),
                "html" => "",
                "subject" => $subject,
                "from_email" => $_ENV['SENDER'],
                "from_name" => "e-Actes sous signature privée",
                "to" => [
                    [
                        "email" => $u['email'],
                        "name" => "",
                        "type" => "to"
                    ]

                ],
                "track_opens" => "true",
                "track_clicks" => "true",
                "tags" => [
                    "assp",
                    $_ENV['ENVIRONMENT']
                ],
                "subaccount" => "assp"
            ],
            "async" => false,
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
            CURLOPT_URL => 'https://mandrillapp.com/api/1.0/messages/send-template.json',
            CURLOPT_POSTFIELDS => $data_string,

        );
        if (strpos($u['email'], '@cnb') == false) {
            curl_setopt_array($ch, $options);
            $output = curl_exec($ch);
            $result = json_decode($output, true);
            if (isset($result['error'])) {
                $this->logger->error($result['error'] . ' ' . $result['message'] . ' Signature email from ' . $user->getCnbId() . ' to ' . $u['email'] . ' acte :' . $act->getFolderNUmber() . ' sujet : ' . $subject);
            } else {
                $this->logger->info('Signature email from ' . $user->getCnbId() . ' to ' . $u['email'] . ' acte :' . $act->getFolderNUmber() . ' sujet : ' . $subject);
            }
        } else {
            $this->logger->error('Signature email contains @cnb from ' . $user->getCnbId() . ' to ' . $u['email'] . ' acte :' . $act->getFolderNUmber() . ' sujet : ' . $subject);
        }

        return true;
    }


    public function sendSignNotificationEmail($initiator, $signatory, $subject, $act)
    {
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $data = array(
            "key" => "6vp_r7CzdJCaedGUU0Jecw",
            "template_name" => "signing_notification",
            "template_content" => array(
                array(
                    'name' => 'signataire',
                    'content' => strtoupper($signatory->getName()) . ' ' . $signatory->getLastName() . ' '
                ),
                array(
                    'name' => 'email',
                    'content' => $signatory->getEmail()
                ),
                array(
                    'name' => 'phoneNumber',
                    'content' => $signatory->getCodeCountry().$signatory->getPhoneNumber()
                ),
                array(
                    'name' => 'act',
                    'content' => $act->getName()
                ),
            ),
            "message" => [
                "global_merge_vars" => array(
                    array(
                        "name" => "LINK",
                        "content" => $_ENV['HOST']
                    )
                ),
                "html" => "",
                "subject" => $subject,
                "from_email" => $_ENV['SENDER'],
                "from_name" => "e-Actes sous signature privée",
                "to" => [
                    [
                        "email" => $initiator->getEmailApp() ? $initiator->getEmailApp() : $initiator->getEmail(),
                        "name" => $initiator->getLastName().' '.$initiator->getName(),
                        "type" => "to"
                    ]

                ],
                "track_opens" => "true",
                "track_clicks" => "true",
                "tags" => [
                    "assp",
                    $_ENV['ENVIRONMENT']
                ],
                "subaccount" => "assp"
            ],
            "async" => false,
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
            CURLOPT_URL => 'https://mandrillapp.com/api/1.0/messages/send-template.json',
            CURLOPT_POSTFIELDS => $data_string,

        );
        if(strpos( $initiator->getEmail(), '@cnb') == false) {
            curl_setopt_array($ch, $options);
            $output = curl_exec($ch);
            $result = json_decode($output, true);
        }
        return true;
    }

    public function sendValiationNotifToInitiator($initiator, $signatories, $subject)
    {
        $name = [];
        foreach ($signatories as $signatory) {
            $contact = $this->entityManager->getRepository(Contact::class)->findOneBy(array('initiator' => $initiator, 'contact' => $signatory->getUser()));
            $concat = strtoupper($contact->getName()). ' ' . $contact->getLastName() . ' ';
            array_push($name, $concat);
        }
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $data = array(
            "key" => "6vp_r7CzdJCaedGUU0Jecw",
            "template_name" => "validation_notification",
            "template_content" => array(
                array(
                    'name' => 'act',
                    'content' => $signatories[0]->getAct()->getName()
                ),
            ),
            "message" => [
                'merge' => true,
                'merge_language' => 'handlebars',
                "global_merge_vars" => array(
                    array(
                        'name' => 'SIG',
                        "content" => $name
                    ),
                    array(
                        "name" => "LINK",
                        "content" => $_ENV['HOST']
                    )
                ),
                "html" => "",
                "subject" => $subject,
                "from_email" => $_ENV['SENDER'],
                "from_name" => "e-Actes sous signature privée",
                "to" => [
                    [
                        "email" => $initiator->getEmailApp() ? $initiator->getEmailApp() : $initiator->getEmail(),
                        "name" => $initiator->getLastName().' '.$initiator->getName(),
                        "type" => "to"
                    ]

                ],
                "track_opens" => "true",
                "track_clicks" => "true",
                "tags" => [
                    "assp",
                    $_ENV['ENVIRONMENT']
                ],
                "subaccount" => "assp"
            ],
            "async" => false,
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
            CURLOPT_URL => 'https://mandrillapp.com/api/1.0/messages/send-template.json',
            CURLOPT_POSTFIELDS => $data_string,

        );
        if(strpos( $initiator->getEmail(), '@cnb') == false) {
            curl_setopt_array($ch, $options);
            $output = curl_exec($ch);
            $result = json_decode($output, true);
        }
        return true;
    }

    /**
     * @param $user
     * @param $u
     * @param $actId
     * @param $subject
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function downloadFileEmailMessage($user, $u, $actId, $subject)
    {
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $contact = $this->entityManager->getRepository(Contact::class)->findOneBy(array('contact'=>$u['id'],'initiator'=>$this->security->getUser()));
        $userRecepient = $this->entityManager->getRepository(User::class)->find($u['id']);
        $id = $userRecepient->getId();
        $data = array(
            "key" => "6vp_r7CzdJCaedGUU0Jecw",
            "template_name" => "downloadFile",
            "template_content" => array(
                array(
                    'name' => 'expiditeur',
                    'content' => $user->getLastName() . ' '.$user->getName() . ' '
                ),
                array(
                    'name' => 'email',
                    'content' => $user->getEmail()
                ),
            ),
            "message" => [
                "global_merge_vars" => array(
                    array(
                        "name" => "LINK",
                        "content" => $_ENV['HOST'] . 'download_document?act=' . $actId .'&id='.$id. '&timestamp=' . $timestamp
                    )
                ),
                "html" => "",
                "subject" => $subject,
                "from_email" => $_ENV['SENDER'],
                "from_name" => "e-Actes sous signature privée",
                "to" => [
                    [
                        "email" => $u['email'],
                        "name" =>  "",
                        "type" => "to"
                    ]

                ],
                "track_opens" => "true",
                "track_clicks" => "true",
                "tags" => [
                    "assp",
                    $_ENV['ENVIRONMENT']
                ],
                "subaccount" => "assp"
            ],
            "async" => false,
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
            CURLOPT_URL => 'https://mandrillapp.com/api/1.0/messages/send-template.json',
            CURLOPT_POSTFIELDS => $data_string,

        );
        curl_setopt_array($ch, $options);
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        return true;
    }


    public function sendValidatorNotificationToInitiator($initiator, $signatory, $subject, $act)
    {
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $data = array(
            "key" => "6vp_r7CzdJCaedGUU0Jecw",
            "template_name" => "validators_notification",
            "template_content" => array(
                array(
                    'name' => 'signataire',
                    'content' => strtoupper($signatory->getName()) . ' ' . $signatory->getLastName() . ' '
                ),
                array(
                    'name' => 'email',
                    'content' => $signatory->getEmail()
                ),
                array(
                    'name' => 'phoneNumber',
                    'content' => $signatory->getCodeCountry().$signatory->getPhoneNumber()
                ),
                array(
                    'name' => 'act',
                    'content' => $act->getName()
                ),
            ),
            "message" => [
                "global_merge_vars" => array(
                    array(
                        "name" => "LINK",
                        "content" => $_ENV['HOST']
                    )
                ),
                "html" => "",
                "subject" => $subject,
                "from_email" => $_ENV['SENDER'],
                "from_name" => "e-Actes sous signature privée",
                "to" => [
                    [
                        "email" => $initiator->getEmailApp() ? $initiator->getEmailApp() : $initiator->getEmail(),
                        "name" => $initiator->getLastName().' '.$initiator->getName(),
                        "type" => "to"
                    ]

                ],
                "track_opens" => "true",
                "track_clicks" => "true",
                "tags" => [
                    "assp",
                    $_ENV['ENVIRONMENT']
                ],
                "subaccount" => "assp"
            ],
            "async" => false,
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
            CURLOPT_URL => 'https://mandrillapp.com/api/1.0/messages/send-template.json',
            CURLOPT_POSTFIELDS => $data_string,

        );
        if(strpos( $initiator->getEmail(), '@cnb') == false) {
            curl_setopt_array($ch, $options);
            $output = curl_exec($ch);
            $result = json_decode($output, true);
        }
        return true;
    }

    public function sendRefuseValidatorNotificationToInitiator($initiator, $signatory, $subject, $act)
    {
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $data = array(
            "key" => "6vp_r7CzdJCaedGUU0Jecw",
            "template_name" => "refuse_validator_notification",
            "template_content" => array(
                array(
                    'name' => 'signataire',
                    'content' => strtoupper($signatory->getName()). ' ' . $signatory->getLastName() . ' '
                ),
                array(
                    'name' => 'email',
                    'content' => $signatory->getEmail()
                ),
                array(
                    'name' => 'phoneNumber',
                    'content' => $signatory->getCodeCountry().$signatory->getPhoneNumber()
                ),
                array(
                    'name' => 'act',
                    'content' => $act->getName()
                ),
            ),
            "message" => [
                "global_merge_vars" => array(
                    array(
                        "name" => "LINK",
                        "content" => $_ENV['HOST']
                    )
                ),
                "html" => "",
                "subject" => $subject,
                "from_email" => $_ENV['SENDER'],
                "from_name" => "e-Actes sous signature privée",
                "to" => [
                    [
                        "email" => $initiator->getEmailApp() ? $initiator->getEmailApp() : $initiator->getEmail(),
                        "name" => $initiator->getLastName().' '.$initiator->getName(),
                        "type" => "to"
                    ]

                ],
                "track_opens" => "true",
                "track_clicks" => "true",
                "tags" => [
                    "assp",
                    $_ENV['ENVIRONMENT']
                ],
                "subaccount" => "assp"
            ],
            "async" => false,
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
            CURLOPT_URL => 'https://mandrillapp.com/api/1.0/messages/send-template.json',
            CURLOPT_POSTFIELDS => $data_string,

        );
        if(strpos( $initiator->getEmail(), '@cnb') == false) {
            curl_setopt_array($ch, $options);
            $output = curl_exec($ch);
            $result = json_decode($output, true);
        }
        return true;
    }

    public function allSignedNotificationToInitiator($initiator, $signatories, $subject,$status)
    {
        $act = $this->entityManager->getRepository(Act::class)->find($signatories[0]['act']);
        $name = [];
        foreach ($signatories as $signatory) {
            $concat = strtoupper($signatory['name'])  . ' ' . $signatory['lastName'] . ' ';
            array_push($name, $concat);
        }
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $data = array(
            "key" => "6vp_r7CzdJCaedGUU0Jecw",
            "template_name" => "signatories_signing_notification",
            "template_content" => array(
                array(
                    'name' => 'act',
                    'content' => $act->getName()
                ),
            ),
            "message" => [
                'merge' => true,
                'merge_language' => 'handlebars',
                "global_merge_vars" => array(
                    array(
                        'name' => 'status',
                        'content' => $status
                    ),
                    array(
                        'name' => 'SIG',
                        "content" => $name
                    ),
                    array(
                        "name" => "LINK",
                        "content" => $_ENV['HOST']
                    ),
                    array(
                        'name' => 'act',
                        'content' => $act->getName()
                    )
                ),
                "html" => "",
                "subject" => $subject,
                "from_email" => $_ENV['SENDER'],
                "from_name" => "e-Actes sous signature privée",
                "to" => [
                    [
                        "email" => $initiator->getEmailApp() ? $initiator->getEmailApp() : $initiator->getEmail(),
                        "name" => $initiator->getLastName().' '.$initiator->getName(),
                        "type" => "to"
                    ]

                ],
                "track_opens" => "true",
                "track_clicks" => "true",
                "tags" => [
                    "assp",
                    $_ENV['ENVIRONMENT']
                ],
                "subaccount" => "assp"
            ],
            "async" => false,
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
            CURLOPT_URL => 'https://mandrillapp.com/api/1.0/messages/send-template.json',
            CURLOPT_POSTFIELDS => $data_string,

        );
        if(strpos( $initiator->getEmail(), '@cnb') == false) {
            curl_setopt_array($ch, $options);
            $output = curl_exec($ch);
            $result = json_decode($output, true);
        }
        return true;
    }

    public function NotValidationNotificationToInitiator($initiator, $signatories, $subject)
    {
        $name = [];
        foreach ($signatories as $signatory) {
            $contact = $this->entityManager->getRepository(Contact::class)->findOneBy(array('initiator' => $initiator, 'contact' => $signatory->getUser()));
            $concat = $contact->getName() . ' ' . $contact->getLastName() . ' ';
            array_push($name, $concat);
        }
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $data = array(
            "key" => "6vp_r7CzdJCaedGUU0Jecw",
            "template_name" => "refuse_validators_notification",
            "template_content" => array(
                array(
                    'name' => 'browsers',
                    "content" => [
                        "Chrome",
                        "Firefox",
                        "Explorer",
                        "Safari",
                        "Opera"
                    ]
                ),
                array(
                    'name' => 'act',
                    'content' => $signatories[0]->getAct()->getName()
                ),
            ),
            "message" => [
                'merge' => true,
                'merge_language' => 'handlebars',
                "global_merge_vars" => array(
                    array(
                        'name' => 'SIG',
                        "content" => $name
                    ),
                    array(
                        "name" => "LINK",
                        "content" => $_ENV['HOST']
                    )
                ),
                "html" => "",
                "subject" => $subject,
                "from_email" => $_ENV['SENDER'],
                "from_name" => "e-Actes sous signature privée",
                "to" => [
                    [
                        "email" => $initiator->getEmailApp() ? $initiator->getEmailApp() : $initiator->getEmail(),
                        "name" => $initiator->getLastName().' '.$initiator->getName(),
                        "type" => "to"
                    ]

                ],
                "track_opens" => "true",
                "track_clicks" => "true",
                "tags" => [
                    "assp",
                    $_ENV['ENVIRONMENT']
                ],
                "subaccount" => "assp"
            ],
            "async" => false,
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
            CURLOPT_URL => 'https://mandrillapp.com/api/1.0/messages/send-template.json',
            CURLOPT_POSTFIELDS => $data_string,

        );
        if(strpos( $initiator->getEmail(), '@cnb') == false) {
            curl_setopt_array($ch, $options);
            $output = curl_exec($ch);
            $result = json_decode($output, true);
        }
        return true;
    }

    /**
     * @param $users
     * @param $act
     * @param $subject
     * @param $user
     * @param $status
     * @param $date
     */
    public function deadLineMessage($users, $act, $subject,$user,$status,$date)
    {
        $data = array(
            "key" => "6vp_r7CzdJCaedGUU0Jecw",
            "template_name" => "deadline-init",
            "template_content" => array(
                array(
                    'name' => 'act',
                    'content' => $act->getName()
                ),
                array(
                    'name' => 'users',
                    'content' => $users['name'].' '.$users['lastName']
                ),
                array(
                    'name' => 'date',
                    'content' => $date
                ),
                array(
                    'name' => 'status',
                    'content' => $status
                ),
            ),
            "message" => [
                "global_merge_vars" => array(
                    array(
                        "name" => "LINK",
                        "content" => $_ENV['HOST']
                    )
                ),
                "html" => "",
                "subject" => $subject,
                "from_email" => $_ENV['SENDER'],
                "from_name" => "e-Actes sous signature privée",
                "to" => [
                    [
                        "email" => $user->getEmail(),
                        "name" => $user->getLastName().' '.$user->getName(),
                        "type" => "to"
                    ]

                ],
                "track_opens" => "true",
                "track_clicks" => "true",
                "tags" => [
                    "assp",
                    $_ENV['ENVIRONMENT']
                ],
                "subaccount" => "assp"
            ],
            "async" => false,
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
            CURLOPT_URL => 'https://mandrillapp.com/api/1.0/messages/send-template.json',
            CURLOPT_POSTFIELDS => $data_string,

        );
        if(strpos( $user->getEmail(), '@cnb') == false) {
        curl_setopt_array($ch, $options);
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        }
        return true;
    }

    public function sendBlockedUserToInitiator($initiatorEmail , $name , $lastName , $subject)
    {
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $data = array(
            "key" => "6vp_r7CzdJCaedGUU0Jecw",
            "template_name" => "blocked",
            "template_content" => array(
                array(
                    'name' => 'name',
                    'content' => $name
                ),
                array(
                    'name' => 'lastName',
                    'content' => $lastName
                )
            ),
            "message" => [
                'merge' => true,
                'merge_language' => 'handlebars',
                "global_merge_vars" => array(
                    array(
                        "name" => "LINK",
                        "content" => $_ENV['HOST']
                    ),
                    array(
                        'name' => 'name',
                        'content' => $name
                    ),
                    array(
                        'name' => 'lastName',
                        'content' => $lastName
                    )
                ),
                "html" => "",
                "subject" => $subject,
                "from_email" => $_ENV['SENDER'],
                "from_name" => "e-Actes sous signature privée",
                "to" => [
                    [
                        "email" => $initiatorEmail,
                        "name" => "",
                        "type" => "to"
                    ]

                ],
                "track_opens" => "true",
                "track_clicks" => "true",
                "tags" => [
                    "assp",
                    $_ENV['ENVIRONMENT']
                ],
                "subaccount" => "assp"
            ],
            "async" => false,
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
            CURLOPT_URL => 'https://mandrillapp.com/api/1.0/messages/send-template.json',
            CURLOPT_POSTFIELDS => $data_string,

        );
        curl_setopt_array($ch, $options);
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        return true;
    }


    //Send Stats mail
    public function sendStatsMail($countSignedAct, $countValidatedAct, $countWaitingSignAct, $countOfSignatures, $countInitiator, $month, $year)
    {
        $data = array(
            "key" => "6vp_r7CzdJCaedGUU0Jecw",
            "template_name" => "monthly_stats",
            "template_content" => array(
                array(
                    'name' => 'countSignedAct',
                    'content' => $countSignedAct
                ),
                array(
                    'name' => 'countValidatedAct',
                    'content' => $countValidatedAct
                ),
                array(
                    'name' => 'countWaitingSignAct',
                    'content' => $countWaitingSignAct
                ),
                array(
                    'name' => 'countOfSignatures',
                    'content' => $countOfSignatures
                ),
                array(
                    'name' => 'countInitiator',
                    'content' => $countInitiator
                ),
                array(
                    'name' => 'month',
                    'content' => $month
                ),
                array(
                    'name' => 'year',
                    'content' => $year
                ),

            ),
            "message" => [
                "global_merge_vars" => array(
                    array(
                        "name" => "LINK",
                        "content" => $_ENV['HOST']
                    )
                ),
                "html" => "",
                "subject" => "e-ASSP - Statistiques " . $month,
                "from_email" => $_ENV['SENDER'],
                "from_name" => "e-Actes sous signature privée",
                "to" => [
                    [
                        "email" => 'slim.besbes@yellow-it.fr',
                        "type" => "to"
                    ],
                    [
                        "email" => 'b.cisse-EXT@cnb.avocat.fr',
                        "type" => "to"
                    ],
                    [
                        "email" => 'M.FERREIRA@cnb.avocat.fr',
                        "type" => "to"
                    ]
                ],
                "track_opens" => "true",
                "track_clicks" => "true",
                "tags" => [
                    "assp",
                    $_ENV['ENVIRONMENT']
                ],
                "subaccount" => "assp"
            ],
            "async" => false,
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
            CURLOPT_URL => 'https://mandrillapp.com/api/1.0/messages/send-template.json',
            CURLOPT_POSTFIELDS => $data_string,

        );
        curl_setopt_array($ch, $options);
        $output = curl_exec($ch);
        $result = json_decode($output, true);
        return true;
    }
}