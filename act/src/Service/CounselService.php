<?php


namespace App\Service;


use App\Entity\Act;
use App\Entity\ActUser;
use App\Entity\Archive;
use App\Entity\Contact;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;


class CounselService
{
    private $entityManager;
    private $security;
    private $logger;


    public function __construct(EntityManagerInterface $entityManager, Security $security, LoggerInterface $appLogger)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->logger = $appLogger;
    }

    public function franceTerritories($phoneNumber , $originalCodeCountry)
    {
        $code = substr($phoneNumber , 0,3);
        if (($code == '690' or $code == '691') and $originalCodeCountry == '+33'){
            $countryCode =  '+590';
        }else if ($code == '694' and $originalCodeCountry == '+33'){
            $countryCode = '+592';
        }else if (($code == '696' or $code == '697') and $originalCodeCountry == '+33'){
            $countryCode = '+596';
        }else if (($code == '639' or $code == '692' or $code == '693') and $originalCodeCountry == '+33' ){
            $countryCode = '+262';
        }else{
            $countryCode = $originalCodeCountry;
        }
        return $countryCode;
    }


    /**
     * @param $counselArray
     * @param $user
     * @return bool
     */
    public function newCounsel($counselArray, $user)
    {
        $date = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);
        $id = $counselArray['actId'];
        $act = $this->entityManager->getRepository(Act::class)->find($id);
        //autocomplete
        if (isset($counselArray['id']) and $counselArray['id'] != null) {
            $counsel = $this->entityManager->getRepository(User::class)->find($counselArray['id']);
            $contact = $this->entityManager->getRepository(Contact::class)->findOneBy(array('contact' => $counselArray['id'], 'initiator' => $this->security->getUser()));
            $actUser = new ActUser();
            $archive = new Archive();
            $userAct = $this->entityManager->getRepository(ActUser::class)->findBy(['user' => $counsel, 'act' => $act]);
            if (sizeof($userAct) != 0 ) {
                return 'exist';
            }
            if (sizeof($userAct) == 0) {
                $actUser->setUser($counsel);
                $actUser->setAct($act);

                $archive->setAct($act);
                $archive->setUser($this->security->getUser());
                $archive->setActionDate($date);
                $archive->setActor($this->security->getUser()->getLastName().' '.$this->security->getUser()->getName() );
                $archive->setAction('Action: Modification de l\'acte : Ajout de contact '.$counsel->getName().' '.$counsel->getLastName());

                $this->entityManager->persist($archive);

                $this->entityManager->persist($actUser);
                $this->entityManager->flush();
            }
            if ($contact == null) {
                $newContact = new Contact();
                $newContact->setContact($counsel);
                $newContact->setInitiator($this->security->getUser());
                $newContact->setLastname($counselArray['lastName']);
                $newContact->setName($counselArray['name']);
                $newContact->setPhoneNumber(($counselArray['phoneNumber'][0] == '0') ? substr($counselArray['phoneNumber'], 1) : $counselArray['phoneNumber']);
                $parsedPhone = $counselArray['phoneNumber'][0] == '0' ? substr($counselArray['phoneNumber'], 1) : $counselArray['phoneNumber'];
                $filterCode = (!isset($counselArray['codeCountry']) or $counselArray['codeCountry'] == null) ? '+33' : $counselArray['codeCountry'];
                $codeCountry = $this->franceTerritories($parsedPhone , $filterCode);
                $newContact->setCodeCountry($codeCountry);
                $newContact->setEmail($counselArray['email']);
                if($counsel->getEmail()!=$counselArray['email'] && $counsel->getEmailApp()!=$counselArray['email']){
                    $newContact->setEmailApp($counselArray['email']);
                }
                if($counsel->getPhoneNumberApp()!=$counselArray['phoneNumber'] && $counsel->getPhoneNumber()!=$counselArray['phoneNumber']){
                    $newContact->setPhoneNumberApp(($counselArray['phoneNumber'][0] == '0') ? substr($counselArray['phoneNumber'], 1) : $counselArray['phoneNumber']);
                    $newContact->setCodeCountryApp((!isset($counselArray['codeCountry']) or $counselArray['codeCountry']==null) ?'+33':$counselArray['codeCountry']);
                }
                $newContact->setModificationDate($date);
                $this->entityManager->persist($newContact);
                $this->entityManager->flush();
                $this->logger->info('Adding a new lawyer from autoComplete ' . substr_replace($newContact->getName(), "*****", 3) . ' ' . substr_replace($newContact->getLastName(), "*****", 3) . ' email ' . substr_replace($newContact->getEmail(), "*****", 3) . ' on act ' . ($act->getFolderNumber() ? $act->getFolderNumber() : $act->getId()) . ' by cnbId ' . $act->getInitiator()->getCnbId());

            } else {
                $contact->setContact($counsel);
                $contact->setInitiator($this->security->getUser());
                $contact->setLastname($counselArray['lastName']);
                $contact->setName($counselArray['name']);
                $contact->setPhoneNumber(($counselArray['phoneNumber'][0] == '0') ? substr($counselArray['phoneNumber'], 1) : $counselArray['phoneNumber']);
                $parsedPhone = $counselArray['phoneNumber'][0] == '0' ? substr($counselArray['phoneNumber'], 1) : $counselArray['phoneNumber'];
                $filterCode = (!isset($counselArray['codeCountry']) or $counselArray['codeCountry'] == null) ? '+33' : $counselArray['codeCountry'];
                $codeCountry = $this->franceTerritories($parsedPhone , $filterCode);
                $contact->setCodeCountry($codeCountry);
                $contact->setEmail($counselArray['email']);
                if($counsel->getEmail()!=$counselArray['email'] && $counsel->getEmailApp()!=$counselArray['email']){
                    $contact->setEmailApp($counselArray['email']);
                }
                if($counsel->getPhoneNumberApp()!=$counselArray['phoneNumber'] && $counsel->getPhoneNumber()!=$counselArray['phoneNumber']){
                    $contact->setPhoneNumberApp(($counselArray['phoneNumber'][0] == '0') ? substr($counselArray['phoneNumber'], 1) : $counselArray['phoneNumber']);
                    $contact->setCodeCountryApp((!isset($counselArray['codeCountry']) or $counselArray['codeCountry']==null) ?'+33':$counselArray['codeCountry']);
                }
                $contact->setModificationDate($date);
                $this->entityManager->persist($contact);
                $this->entityManager->flush();
                $this->logger->info('Adding a new lawyer from autoComplete ' . substr_replace($contact->getName(), "*****", 3) . ' ' . substr_replace($contact->getLastName(), "*****", 3) . ' email ' . substr_replace($contact->getEmail(), "*****", 3) . ' on act ' . ($act->getFolderNumber() ? $act->getFolderNumber() : $act->getId()) . ' by cnbId ' . $act->getInitiator()->getCnbId());
            }
            return $counsel->getId();

        }

        $counsel = $this->entityManager->getRepository(User::class)->findOneBy(array('name' => $counselArray['name'], 'email' => $counselArray['email']));
        if (!$counsel) {
            $counsel = new User();
            $actUser = new ActUser();
            $contact = new Contact();
            $archive = new Archive();
            $counsel->setName($counselArray['name']);
            $counsel->setUsername($counselArray['email'].$counselArray['name'].$counselArray['lastName']);
            $counsel->setEnabled(true);
            $counsel->setLastname($counselArray['lastName']);
            $counsel->setPhoneNumber(($counselArray['phoneNumber'][0] == '0') ? substr($counselArray['phoneNumber'], 1) : $counselArray['phoneNumber']);
            $parsedPhone = $counselArray['phoneNumber'][0] == '0' ? substr($counselArray['phoneNumber'], 1) : $counselArray['phoneNumber'];
            $filterCode = (!isset($counselArray['codeCountry']) or $counselArray['codeCountry'] == null) ? '+33' : $counselArray['codeCountry'];
            $codeCountry = $this->franceTerritories($parsedPhone , $filterCode);
            $counsel->setCodeCountry($codeCountry);
            $counsel->setEmail($counselArray['email']);
            $counsel->setPassword('$2y$13$FpYYOpUEUvtUrDlwqJlJfuJBxqglCmauSQwOfRigwiGYfEr9MegPm');
            $counsel->setCreatedBy($user);
            $actUser->setUser($counsel);
            $actUser->setAct($act);
            $counsel->setRoles(['ROLE_COUNSEL']);
            $contact->setModificationDate($date);
            $this->entityManager->persist($counsel);

            $archive->setAct($act);
            $archive->setUser($this->security->getUser());
            $archive->setActionDate($date);
            $archive->setActor($this->security->getUser()->getLastName().' '.$this->security->getUser()->getName() );

            $archive->setAction('Action: Modification de l\'acte : Ajout de contact '.$counsel->getName().' '.$counsel->getLastName());

            $this->entityManager->persist($archive);


            $contact->setContact($counsel);
            $contact->setInitiator($this->security->getUser());
            $contact->setLastname($counselArray['lastName']);
            $contact->setName($counselArray['name']);
            $contact->setPhoneNumber(($counselArray['phoneNumber'][0] == '0') ? substr($counselArray['phoneNumber'], 1) : $counselArray['phoneNumber']);
            $parsedPhone = $counselArray['phoneNumber'][0] == '0' ? substr($counselArray['phoneNumber'], 1) : $counselArray['phoneNumber'];
            $filterCode = (!isset($counselArray['codeCountry']) or $counselArray['codeCountry'] == null) ? '+33' : $counselArray['codeCountry'];
            $codeCountry = $this->franceTerritories($parsedPhone , $filterCode);
            $contact->setCodeCountry($codeCountry);
            $contact->setEmail($counselArray['email']);
            $this->entityManager->persist($contact);
            $this->entityManager->flush();
            $actUser = new ActUser();
            $actUser->setUser($counsel);
            $actUser->setAct($act);
            $this->entityManager->persist($actUser);
            $this->entityManager->flush();
            $this->logger->info('Adding a new lawyer ' . substr_replace($contact->getName(), "*****", 3) . ' ' . substr_replace($contact->getLastName(), "*****", 3) . ' email ' . substr_replace($contact->getEmail(), "*****", 3) . ' on act ' . ($act->getFolderNumber() ? $act->getFolderNumber() : $act->getId()) . ' by cnbId ' . $act->getInitiator()->getCnbId());
            return $counsel->getId();
        }
        return false;

    }

    public
    function deleteCounsel($counselArray)
    {
        $act = $this->entityManager->getRepository(Act::class)->find($counselArray['actId']);
        $counsel = $this->entityManager->getRepository(Contact::class)->findOneBy(array('contact' => $counselArray['id'], 'initiator' => $this->security->getUser()));
        $userAct = $this->entityManager->getRepository(ActUser::class)->findOneBy(array('user' => $counsel->getContact(), 'act' => $act));
        $this->logger->info('Delete lawyer  ' . substr_replace($counsel->getName(), "*****", 3) . ' ' . substr_replace($counsel->getLastName(), "*****", 3) . ' email ' . substr_replace($counsel->getEmail(), "*****", 3) . ' on act ' . ($act->getFolderNumber() ? $act->getFolderNumber() : $act->getId()) . ' by cnbId ' . $act->getInitiator()->getCnbId());
        $this->entityManager->remove($userAct);
        $archive = new Archive();
        $date = new DateTime('@'.strtotime('now'));
        $archive->setAct($act);
        $archive->setUser($this->security->getUser());
        $archive->setActionDate($date);
        $archive->setActor($this->security->getUser()->getLastName().' '.$this->security->getUser()->getName() );

        $archive->setAction('Action: Modification de l\'acte : Suppression de contact '.$counsel->getName().' '.$counsel->getLastName());
        $this->entityManager->persist($archive);
        $this->entityManager->flush();
        return true;
    }

    /**
     * @param $counselArray
     * @param $user
     * @return User|bool|null|object
     */
    public
    function newContact($counselArray, $user)
    {
        $date = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);
        $counsel = $this->entityManager->getRepository(User::class)->findOneBy(['name' => $counselArray['name'], 'email' => $counselArray['email']]);

        if (!$counsel) {
            $counsel = new User();
            $counsel->setName($counselArray['name']);
            $counsel->setUsername($counselArray['email'].$counselArray['name'].$counselArray['lastName']);
            $counsel->setEnabled(true);
            $counsel->setLastname($counselArray['lastName']);
            $counsel->setPhoneNumber(($counselArray['phoneNumber'][0] == '0') ? substr($counselArray['phoneNumber'], 1) : $counselArray['phoneNumber']);
            $counsel->setCodeCountry((!isset($counselArray['codeCountry']) or $counselArray['codeCountry']==null)?'+33':$counselArray['codeCountry']);
            $counsel->setEmail($counselArray['email']);
            $counsel->setPassword('$2y$13$FpYYOpUEUvtUrDlwqJlJfuJBxqglCmauSQwOfRigwiGYfEr9MegPm');
            $counsel->setCreatedBy($user);
            $counsel->setRoles(['ROLE_COUNSEL']);
            $this->entityManager->persist($counsel);
            $this->entityManager->flush();
            $newContact = new Contact();
            $newContact->setInitiator($user);
            $newContact->setContact($counsel);
            $newContact->setEmail($counselArray['email']);
            $newContact->setPhoneNumber(($counselArray['phoneNumber'][0] == '0') ? substr($counselArray['phoneNumber'], 1) : $counselArray['phoneNumber']);
            $newContact->setCodeCountry((!isset($counselArray['codeCountry']) or $counselArray['codeCountry']==null)?'+33':$counselArray['codeCountry']);
            $newContact->setLastname($counselArray['lastName']);
            $newContact->setName($counselArray['name']);
            $newContact->setModificationDate($date);
            $this->entityManager->persist($newContact);
            $this->entityManager->flush();
            $this->logger->info('Create a new contact ' . substr_replace($newContact->getName(), "*****", 3) . ' ' . substr_replace($newContact->getLastName(), "*****", 3) . ' email ' . substr_replace($counsel->getEmail(), "*****", 3) . ' by cnbId ' . $this->security->getUser()->getCnbId());
            return $this->entityManager->getRepository(User::class)->findUserById($newContact->getId());
        } else {
            $signatoryContact = $this->entityManager->getRepository(Contact::class)->findOneBy(['email' => $counselArray['email'], 'name' => $counselArray['name'], 'lastName' => $counselArray['lastName'], 'initiator' => $this->security->getUser()]);
            if (!$signatoryContact) {
                $contact = new Contact();
                $contact->setContact($counsel);
                $contact->setInitiator($this->security->getUser());
                $contact->setLastname($counselArray['lastName']);
                $contact->setName($counselArray['name']);
                $contact->setPhoneNumber(($counselArray['phoneNumber'][0] == '0') ? substr($counselArray['phoneNumber'], 1) : $counselArray['phoneNumber']);
                $counsel->setCodeCountry((!isset($counselArray['codeCountry']) or $counselArray['codeCountry']==null)?'+33':$counselArray['codeCountry']);
                $contact->setEmail($counselArray['email']);
                $contact->setModificationDate($date);
                $this->entityManager->persist($contact);
                $this->entityManager->flush();
                $this->logger->info('Create a new contact ' . substr_replace($contact->getName(), "*****", 3) . ' ' . substr_replace($contact->getLastName(), "*****", 3) . ' email ' . substr_replace($counsel->getEmail(), "*****", 3) . ' by cnbId ' . $this->security->getUser()->getCnbId());

                return $this->entityManager->getRepository(User::class)->findUserById($contact->getId());
            }
        }
        return false;

    }

    /**
     * @param $user
     * @return string
     */
    public
    function deleteContact($user)
    {
        $userToDelete = $this->entityManager->getRepository(User::class)->find($user['id']);
        $counsel = $this->entityManager->getRepository(Contact::class)->findOneBy(array('contact' => $user['id'], 'initiator' => $this->security->getUser()));
        if ($userToDelete) {
            $actUser = $this->entityManager->getRepository(ActUser::class)->findBy(array('user' => $userToDelete->getId()));
            if ($userToDelete->getCnbId() != null) {
                return 'user edentitas';
            }
//            if (sizeof($actUser) != 0) {
//                return 'user in act';
//            }
            $this->logger->info('Delete contact ' . substr_replace($counsel->getName(), "*****", 3) . ' ' . substr_replace($counsel->getLastName(), "*****", 3) . ' email ' . substr_replace($counsel->getEmail(), "*****", 3) . 'by cnbId ' . $this->security->getUser()->getCnbId());
            $this->entityManager->remove($counsel);
            $this->entityManager->flush();
            return 'user deleted';
        }
        return 'user not found';

    }

    /**
     * @param $counselArray
     * @return bool
     */
    public
    function updateContact($counselArray)
    {
        $date = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);
        $counsel = $this->entityManager->getRepository(Contact::class)->findOneBy(array('contact' => $counselArray['id'], 'initiator' => $this->security->getUser()));

        if ($counsel) {
            $counsel->setName($counselArray['name']);
            $counsel->setLastname($counselArray['lastName']);
            $counsel->setPhoneNumber(($counselArray['phoneNumber'][0] == '0') ? substr($counselArray['phoneNumber'], 1) : $counselArray['phoneNumber']);
            $counsel->setCodeCountry((!isset($counselArray['codeCountry']) or $counselArray['codeCountry']==null)?'+33':$counselArray['codeCountry']);
            if (isset($counselArray['phoneNumberApp'])) {
                $counsel->setPhoneNumberApp(($counselArray['phoneNumber'][0] == '0') ? substr($counselArray['phoneNumber'], 1) : $counselArray['phoneNumber']);
            }
            $counsel->setEmail($counselArray['email']);
            if (isset($counselArray['emailApp'])) {
                $counsel->setPhoneNumberApp($counselArray['emailApp']);
            }
            $counsel->setModificationDate($date);
            $this->entityManager->persist($counsel);
            $this->entityManager->flush();
            $this->logger->info('Update contact counsel' . substr_replace($counsel->getName(), "*****", 3) . ' ' . substr_replace($counsel->getLastName(), "*****", 3) . ' email ' . substr_replace($counsel->getEmail(), "*****", 3) . ' by cnbId ' . $this->security->getUser()->getCnbId());
            return $this->entityManager->getRepository(User::class)->findUserById($counsel->getId());
        }
        return false;

    }


}