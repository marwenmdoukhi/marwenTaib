<?php
/**
 * Created by PhpStorm.
 * User: Helmi
 * Date: 27/01/2020
 * Time: 12:04
 */

namespace App\Service;


use App\Entity\Act;
use App\Entity\ActUser;
use App\Entity\Archive;
use App\Entity\Contact;
use App\Entity\Document;
use App\Entity\User;
use App\Repository\ActRepository;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class ActService
 * @package App\Service
 */
class ActService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * @var MailService
     */
    private $mailService;
    /**
     * @var DocumentService
     */
    private $documentService;
    protected $projectDir;
    private $logger;
    /**
     * @var Security
     */
    private $security;

    public function __construct(EntityManagerInterface $entityManager, OrderService $orderService, MailService $mailService, DocumentService $documentService, KernelInterface $kernel, LoggerInterface $appLogger, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->orderService = $orderService;
        $this->mailService = $mailService;
        $this->documentService = $documentService;
        $this->projectDir = $kernel->getProjectDir();
        $this->logger = $appLogger;
        $this->security = $security;
    }

    /**
     * @param $actArray
     * @param $user
     * @return int
     */
    public function newAct($actArray, $user)
    {
        $date = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);
        $act = new Act();
        $act->setName($actArray['name']);
        $act->setInternalNumber(!array_key_exists('internalNumber', $actArray) ? null : $actArray['internalNumber']);
        $act->setFolderName($actArray['folderName']);
        $act->setExpirationDate(DateTime::createFromFormat("Y-m-d", $actArray['expirationDate'] ));
        $act->setRequestDate($date);
        $act->setStatus('En Projet');
        $act->setInitiator($user);
        $this->entityManager->persist($act);
        $archive = new Archive();
        $archive->setAct($act);
        $archive->setAction('Action: Création de l\'acte ( ' . $act->getName() . ' ) Statut : En Projet');
        $archive->setUser($user);
        $archive->setActor($user->getLastName() . ' ' . $user->getName());
        $archive->setActionDate($date);
        $this->entityManager->persist($archive);

        $this->entityManager->flush();

        $result = $this->entityManager->getRepository(Act::class)->findActById($act->getId());
        $this->logger->info('Create acte En projet actName: ' . $act->getName() . ' actId :' . $act->getId() . ' cnbId : ' . $user->getCnbId());

        return $result;
    }

    /**
     * @param $user
     * @return array
     */
    public function getActs($user): array
    {
        $acts = $this->entityManager->getRepository(Act::class)->findActsByInitiator($user);
        $actsJson = [];

        foreach ($acts as $act) {
            $act['requestDate'] = $act['requestDate']->format('d/m/Y H:i:s');
            $act['lastResentDate'] = $act['lastResentDate'] == null ? null : $act['lastResentDate']->format('d/m/Y H:i');
            $act['receptionDate'] = $act['receptionDate'] == null ? null : $act['receptionDate']->format('d/m/Y H:i:s');
            $act['signingDate'] = $act['signingDate'] == null ? null : $act['signingDate']->format('d/m/Y');
            $act['expirationDate'] = $act['expirationDate'] == null ? null : $act['expirationDate']->format('d/m/Y');

            array_push($actsJson, $act);
        }
        return $actsJson;
    }


    public function getCouncelActs($user): array
    {
        $acts = $this->entityManager->getRepository(Act::class)->findCouncelActs($user);
        $councelActs = [];
        foreach ($acts as $act) {
            $act['requestDate'] = $act['requestDate']->format('d/m/Y H:i:s');
            $act['lastResentDate'] = $act['lastResentDate'] == null ? null : $act['lastResentDate']->format('d/m/Y H:i');
            $act['signingDate'] = $act['signingDate'] == null ? null : $act['signingDate']->format('d/m/Y');
            $act['receptionDate'] = $act['receptionDate'] == null ? null : $act['receptionDate']->format('d/m/Y H:i:s');

            array_push($councelActs, $act);
        }
        return $councelActs;
    }

    /**
     * @param $id
     */
    public function getActById($id)
    {
        $act = null;
        $actCounsel = $this->entityManager->getRepository(Act::class)->findActByIdForUser($id, $this->security->getUser());
        $signatoriesAct = $this->entityManager->getRepository(ActUser::class)->findBy(array('user' => $this->security->getUser(), 'act' => $id));
        if ($actCounsel !== null or sizeof($signatoriesAct) > 0) {
            $act = $this->entityManager->getRepository(Act::class)->findActById($id);
            $act['requestDate'] = $act['requestDate']->format('d/m/Y H:i:s');
            $act['lastResentDate'] = $act['lastResentDate'] == null ? null : $act['lastResentDate']->format('d/m/Y H:i');
            $act['signingDate'] = $act['signingDate'] == null ? null : $act['signingDate']->format('d/m/Y');
            $act['receptionDate'] = $act['receptionDate'] == null ? null : $act['receptionDate']->format('d/m/Y H:i:s');
        }

        return $act;
    }

    public function updateAct($actArray, $user)
    {
        $date = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);
        $act = $this->entityManager->getRepository(Act::class)->find(intval($actArray['id']));
        if ($act->getStatus() == 'En Projet') {
            $archive = new Archive();
            $archive->setAct($act);
            $archive->setAction('Action: Création de l\'acte ( ' . $actArray['folderNumber'] . ' ) Statut : Créé');
            $archive->setUser($user);
            $archive->setActor($user->getLastName() . ' ' . $user->getName());
            $archive->setActionDate($date);
            $this->entityManager->persist($archive);
        }
        $act->setName($actArray['name']);
        $act->setInternalNumber(!array_key_exists('internalNumber', $actArray) ? null : $actArray['internalNumber']);
        $act->setFolderName($actArray['folderName']);
        $act->setRequestDate($date);
        $act->setStatus($actArray['status']);
        $act->setFolderNumber($actArray['folderNumber']);
        $act->setInitiator($user);
        $this->entityManager->persist($act);
        if ($act->getStatus() == 'Abandonne') {
            $archive = new Archive();
            $archive->setAct($act);
            $archive->setAction('Action: L\'acte ( ' . $act->getFolderNumber() . 'est Abondonné' . ' )');
            $archive->setUser($user);
            $archive->setActor($user->getLastName() . ' ' . $user->getName());
            $archive->setActionDate($date);
            $this->entityManager->persist($archive);
        }


        $this->entityManager->flush();
        $result = $this->entityManager->getRepository(Act::class)->findActById($act->getId());
        $this->logger->info('Update acte Cree actName: ' . $act->getName() . ' actId :' . $act->getId() . ' cnbId : ' . $user->getCnbId() . ' FolderNumber : ' . $act->getFolderNumber());


        return $result;
    }

    /**
     * @param $user
     * @return bool
     */
    public function setValidator($user)
    {
        $currentUser = $this->entityManager->getRepository(User::class)->find($user['id']);
        $act = $this->entityManager->getRepository(Act::class)->find($user['actId']);
        if ($currentUser == null) {
            $currentUser = $this->entityManager->getRepository(Contact::class)->findOneBy(array('contact' => $user['id'], 'initiator' => $act->getInitiator()));
            $id = $currentUser->getContact()->getId();
            $lastName = $currentUser->getContact()->getLastName();
            $name = $currentUser->getContact()->getName();
        } else {
            $id = $currentUser->getId();
            $lastName = $currentUser->getLastName();
            $name = $currentUser->getName();
        }
        $actUser = $this->entityManager->getRepository(ActUser::class)->findOneBy(array('act' => $user['actId'], 'user' => $id));
        $actUser->setValidator(true);
        $actUser->setMailSent(true);
        $this->entityManager->persist($actUser);
        $this->entityManager->flush();
        $this->logger->info('Set validator on act: ' . $act->getName() . ' cnbId : ' . $act->getInitiator()->getCnbId() . ' FolderNumber : ' . $act->getFolderNumber() . ' validator lastName: ' . $name . ' validator name: ' . $lastName);

        return true;
    }

    /**
     * @param $act
     * @return bool
     */
    public function deleteAct($act)
    {
        $act = $this->entityManager->getRepository(Act::class)->find($act['id']);
        $actUser = $this->entityManager->getRepository(ActUser::class)->findBy(array('act' => $act->getId()));
        foreach ($actUser as $au) {
            $this->entityManager->remove($au);
            $this->entityManager->flush();
        }
        $documents = $this->entityManager->getRepository(Document::class)->findBy(array('act' => $act->getId()));
        foreach ($documents as $doc) {
            $this->entityManager->remove($doc);
            $this->entityManager->flush();
        }
        $id = $act->getId();
        if (is_dir("$this->projectDir/src/assets/documents/$id")) {
            array_map('unlink', glob("$this->projectDir/src/assets/documents/$id/*.*"));
            rmdir("$this->projectDir/src/assets/documents/$id");
        }
        $this->logger->info('Delete act: ' . $act->getName() . ' cnbId : ' . $act->getInitiator()->getCnbId() . ' actId : ' . $act->getId());
        $this->entityManager->remove($act);
        $this->entityManager->flush();


        return true;
    }

    /**
     * @param $user
     * @return array
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function validateAct($user)
    {
        $date = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Paris');
        $archive = new Archive();
        $date->setTimezone($timezone);
        $actUser = $this->entityManager->getRepository(ActUser::class)->findOneBy(array('act' => $user['actId'], 'user' => $user['id']));
        $actUser->setActValidated(true);
        $actUser->setValidatedAt($date);
        $this->entityManager->persist($actUser);
        $archive->setAct($actUser->getAct());
        $archive->setUser($actUser->getUser());
        $archive->setActor($actUser->getUser()->getLastName() . ' ' . $actUser->getUser()->getName());

        $archive->setActionDate($date);
        $archive->setAction('Action: Validation de l\'acte - Accepté');
        $this->entityManager->persist($archive);

        $this->entityManager->flush();
        $this->logger->info('Act validated: ' . $actUser->getAct()->getName() . ' cnbId : ' . $actUser->getAct()->getInitiator()->getCnbId() . ' FolderNumber : ' . $actUser->getAct()->getFolderNumber() . ' validated by : ' . $actUser->getUser()->getName() . ' ' . $actUser->getUser()->getLastName());

        $actUsers = $this->entityManager->getRepository(ActUser::class)->findBy(array('act' => $user['actId'], 'validator' => true));
        $actUsersWithStatus = $this->entityManager->getRepository(ActUser::class)->findBy(array('act' => $user['actId'], 'validator' => true, 'actValidated' => true));
        if (sizeof($actUsers) == sizeof($actUsersWithStatus)) {
            $status = 'e-ASSP - Acte à signer électroniquement envoyé par votre avocat ';
            $act = $this->entityManager->getRepository(Act::class)->find($user['actId']);
            $initiator = $act->getInitiator();
            $act->setStatus('En cours de signature');
            $act->setRequestDate($date);
            $this->entityManager->persist($act);
            $this->entityManager->flush();
            $this->mailService->sendValiationNotifToInitiator($initiator, $actUsers, $status);
            $this->documentService->mergeDocuments([$act->getId() . '']);
            $actUsersSignatory = $this->entityManager->getRepository(ActUser::class)->findSignatory($act->getId());

            if ($act->getExpirationDate() !== null ) {

                $now = new DateTime();
                $difference = $act->getExpirationDate()->diff($now)->format('%a');
                $remainingDays = strval($difference);
            }
            else {
                $remainingDays = "21";
            }

            foreach ($actUsersSignatory as $aus) {
                $userTosend = $act->getInitiator();
                $contact = $this->entityManager->getRepository(Contact::class)->findOneBy(array('initiator' => $userTosend, 'contact' => $aus['id']));
                $status = 'Acte Sous Signature privée signé';
                $ausArray['email'] = $contact->getEmail();
                $ausArray['id'] = $aus['id'];
                $ausArray['actId'] = $act->getId();
                try {
                    $this->mailService->sendSigningEmailMessage($userTosend, $ausArray, $status, null, $remainingDays);
                } catch (LoaderError $e) {
                } catch (RuntimeError $e) {
                } catch (SyntaxError $e) {
                }
            }

        }
        $actUsersWithStatusNotNull = $this->entityManager->getRepository(ActUser::class)->findBy(array('act' => $user['actId'], 'validator' => true, 'actValidated' => null));
        $actUsersWithStatusNull = $this->entityManager->getRepository(ActUser::class)->findBy(array('act' => $user['actId'], 'validator' => true, 'actValidated' => false));
        if (sizeof($actUsersWithStatusNull) != 0 && sizeof($actUsersWithStatusNotNull) == 0) {
            $act = $this->entityManager->getRepository(Act::class)->find($user['actId']);
            $act->setStatus('Validation refusee');
            $act->setRequestDate($date);
            $this->entityManager->persist($act);
            $this->entityManager->flush();
            $status = 'Acte à valider';
            $signatories = [];
            foreach ($actUsersWithStatusNull as $au) {
                array_push($au->getUser(), $signatories);
            }
            $this->mailService->NotValidationNotificationToInitiator($act->getInitiator(), $signatories, $status);
        }
        return $this->getActById($user['actId']);
    }


    public function refuseAct($user)
    {
        $archive = new Archive();
        $date = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);
        $act = $this->entityManager->getRepository(Act::class)->find($user['actId']);
        $act->setRequestDate($date);
        $actUser = $this->entityManager->getRepository(ActUser::class)->findOneBy(array('act' => $user['actId'], 'user' => $user['id']));
        $actUser->setActValidated(false);
        $actUser->setValidatedAt($date);
        if (isset($user['comment'])) {
            $actUser->setComment($user['comment']);
        }
        $this->entityManager->persist($actUser);
        $this->entityManager->persist($act);

        $archive->setAct($actUser->getAct());
        $archive->setUser($actUser->getUser());
        $archive->setActionDate($date);
        $archive->setActor($actUser->getUser()->getLastName() . ' ' . $actUser->getUser()->getName());
        $archive->setAction('Action: Validation de l\'acte - Réfusé');
        $this->entityManager->persist($archive);
        $this->logger->info('Act refused: ' . $actUser->getAct()->getName() . ' cnbId : ' . $actUser->getAct()->getInitiator()->getCnbId() . ' FolderNumber : ' . $actUser->getAct()->getFolderNumber() . ' refused by : ' . $actUser->getUser()->getName() . ' ' . $actUser->getUser()->getLastName());


        $this->entityManager->flush();
        $actUsersWithStatus = $this->entityManager->getRepository(ActUser::class)->findBy(array('act' => $user['actId'], 'validator' => true, 'actValidated' => false));
        $actUsersWithStatusNotNull = $this->entityManager->getRepository(ActUser::class)->findBy(array('act' => $user['actId'], 'validator' => true, 'actValidated' => null));
        if (sizeof($actUsersWithStatus) != 0 && sizeof($actUsersWithStatusNotNull) == 0) {
            $act = $this->entityManager->getRepository(Act::class)->find($user['actId']);
            $act->setStatus('Validation refusee');
            $this->entityManager->persist($act);
            $this->entityManager->flush();
        }
        return $this->getActById($user['actId']);
    }

    public function neutralUsers($user)
    {
        return $this->entityManager->getRepository(ActUser::class)->findBy(array('act' => $user['actId'], 'validator' => true));
    }

    public function unsignedUsers($user)
    {
        return $this->entityManager->getRepository(ActUser::class)->findSignatoryWithNotSigned($user['actId']);
    }

    public function refuseSignature($user)
    {
        $date = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);
        $actUser = $this->entityManager->getRepository(ActUser::class)->findOneBy(array('act' => $user['actId'], 'user' => $user['id']));
        $actUser->setSignedAt($date);
        if (isset($user['signatureComment'])) {
            $actUser->setSignatureComment($user['signatureComment']);
        }
        $this->entityManager->persist($actUser);
        $this->entityManager->flush();
        $act = $this->entityManager->getRepository(Act::class)->find($user['actId']);
        $act->setStatus('Signature refusee');
        $act->setRequestDate($date);
        $this->entityManager->persist($act);
        $archive = new Archive();
        $archive->setAct($actUser->getAct());
        $archive->setUser($actUser->getUser());
        $archive->setActor($actUser->getUser()->getLastName() . ' ' . $actUser->getUser()->getName());
        $archive->setActionDate($date);
        $archive->setAction('Action: Signature de l\'acte - Réfusé');
        $this->entityManager->persist($archive);
        $this->entityManager->flush();
        $this->logger->info('Act not signed: ' . $actUser->getAct()->getName() . ' cnbId : ' . $actUser->getAct()->getInitiator()->getCnbId() . ' FolderNumber : ' . $actUser->getAct()->getFolderNumber() . ' not signed by : ' . $actUser->getUser()->getName() . ' ' . $actUser->getUser()->getLastName());


        return true;
    }

    public function actArchive($id)
    {
        return $this->entityManager->getRepository(Archive::class)->findArchiveByAct($id);
    }

}