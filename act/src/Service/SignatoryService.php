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

class SignatoryService
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
     * @param $signatoryArray
     * @param $user
     * @return bool
     */
    public function newSignatory($signatoryArray, $user)
    {
        $date = new \DateTime();
        $nature = 'P';
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);
        $id = $signatoryArray['actId'];
        $act = $this->entityManager->getRepository(Act::class)->find($id);
        if(isset($signatoryArray['enterpriseName']) and $signatoryArray['enterpriseName'] !== null){
            $signatory = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $signatoryArray['email'], 'name' => $signatoryArray['name'], 'enterpriseName' => $signatoryArray['enterpriseName']]);
        }else{
            $signatory = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $signatoryArray['email'], 'name' => $signatoryArray['name'],'lastName'=>$signatoryArray['lastName'],'enterpriseName' => null , 'cnbId' => NULL]);
        }
//        dd($signatory);
        if (!$signatory) {
            $actUser = new ActUser();
            $signatory = new User();
            $contact = new Contact();
            $archive = new Archive();
            $contact->setContact($signatory);
            $contact->setInitiator($this->security->getUser());
            $contact->setLastname($signatoryArray['lastName']);
            $contact->setName($signatoryArray['name']);
            $contact->setPhoneNumber(($signatoryArray['phoneNumber'][0] == '0') ? substr($signatoryArray['phoneNumber'], 1) : $signatoryArray['phoneNumber']);
            $parsedPhone = $signatoryArray['phoneNumber'][0] == '0' ? substr($signatoryArray['phoneNumber'], 1) : $signatoryArray['phoneNumber'];
            $filterCode = (!isset($signatoryArray['codeCountry']) or $signatoryArray['codeCountry'] == null) ? '+33' : $signatoryArray['codeCountry'];
            $codeCountry = $this->franceTerritories($parsedPhone , $filterCode);
            $contact->setCodeCountry($codeCountry);
            $contact->setEmail($signatoryArray['email']);


            $signatory->setName($signatoryArray['name']);
            if ( isset($signatoryArray['enterpriseName']) and  $signatoryArray['enterpriseName'] !== null) {
                $signatory->setUsername($signatoryArray['email'].$signatoryArray['name'].$signatoryArray['lastName'].$signatoryArray['enterpriseName']);
            }else{
                $signatory->setUsername($signatoryArray['email'].$signatoryArray['name'].$signatoryArray['lastName'].$user->getCnbId());
            }
            $signatory->isEnabled(false);
            $signatory->setEdentitas(0);
            $signatory->setLastname($signatoryArray['lastName']);
            if (isset($signatoryArray['birthDate']) && $signatoryArray['birthDate'] != null && $signatoryArray['birthDate'] != 'invalid date') {
                $signatory->setBirthDate(DateTime::createFromFormat("d/m/Y", $signatoryArray['birthDate'] ));
                $contact->setBirthDate(DateTime::createFromFormat("d/m/Y", $signatoryArray['birthDate']));
            }
            $parsedPhone = $signatoryArray['phoneNumber'][0] == '0' ? substr($signatoryArray['phoneNumber'], 1) : $signatoryArray['phoneNumber'];
            $signatory->setPhoneNumber(($signatoryArray['phoneNumber'][0] == '0') ? substr($signatoryArray['phoneNumber'], 1) : $signatoryArray['phoneNumber']);
            $filterCode = (!isset($signatoryArray['codeCountry']) or $signatoryArray['codeCountry'] == null) ? '+33' : $signatoryArray['codeCountry'];
            $codeCountry = $this->franceTerritories($parsedPhone , $filterCode);
            $signatory->setCodeCountry($codeCountry);
            $signatory->setEmail($signatoryArray['email']);
            if (isset($signatoryArray['birthPlace']) && $signatoryArray['birthPlace'] != null) {
                $signatory->setBirthPlace($signatoryArray['birthPlace']);
                $contact->setBirthPlace($signatoryArray['birthPlace']);
            }
            $signatory->setPassword($signatoryArray['email']);
            $signatory->setCreatedBy($user);
            $actUser->setUser($signatory);
            $actUser->setAct($act);
            if ($signatoryArray['role'] == 'signatory') {
                $signatory->setRoles(['ROLE_SIGNATORY']);
            } else if ($signatoryArray['role'] == 'enterprise') {
                $signatory->setEnterpriseName($signatoryArray['enterpriseName']);
                $signatory->setSiren($signatoryArray['siren']);
                $contact->setEnterpriseName($signatoryArray['enterpriseName']);
                $contact->setSiren($signatoryArray['siren']);
                $signatory->setRoles(['ROLE_ENTERPRISE']);
                $nature = 'M';
            }
            $contact->setModificationDate($date);



            $this->entityManager->persist($signatory);
            $this->entityManager->persist($actUser);
            $this->entityManager->persist($contact);

            $archive->setAct($act);
            $archive->setUser($this->security->getUser());
            $archive->setActor($this->security->getUser()->getLastName().' '.$this->security->getUser()->getName() );

            $archive->setActionDate($date);
            $archive->setAction('Action: Modification de l\'acte : Ajout de contact '.$signatory->getName().' '.$signatory->getLastName());
            $this->entityManager->persist($archive);


            $this->entityManager->flush();
            $this->logger->info('Adding a new signatory ' . substr_replace($contact->getName(), "*****", 3) . ' ' . substr_replace($contact->getLastName(), "*****", 3) . ' email ' . substr_replace($contact->getEmail(), "*****", 3). ' on act ' . ($act->getFolderNumber() ? $act->getFolderNumber() : $act->getId()) . ' by cnbId ' . $act->getInitiator()->getCnbId().' nature '.$nature);
            return $signatory->getId();
        } else if ($signatory) {
            if (isset($signatoryArray['enterpriseName']) and $signatoryArray['enterpriseName'] !== null ) {
                $signatoryContact = $this->entityManager->getRepository(Contact::class)->findOneBy(['email' => $signatoryArray['email'], 'name' => $signatoryArray['name'],'enterpriseName' => $signatoryArray['enterpriseName'],'initiator' => $this->security->getUser()->getId()]);
            }else{
                $signatoryContact = $this->entityManager->getRepository(Contact::class)->findOneBy(['email' => $signatoryArray['email'], 'name' => $signatoryArray['name'],'lastName'=>$signatoryArray['lastName'],'enterpriseName' => null, 'initiator' => $this->security->getUser()]);
            }
            if (!$signatoryContact) {
                $contact = new Contact();
                $archive = new Archive();
                $contact->setContact($signatory);
                $contact->setInitiator($this->security->getUser());
                $actUser = new ActUser();
                $userAct = $this->entityManager->getRepository(ActUser::class)->findBy(['user' => $signatory, 'act' => $act]);
                $contact->setLastname($signatoryArray['lastName']);
                $contact->setName($signatoryArray['name']);
                if (isset($signatoryArray['birthDate']) && $signatoryArray['birthDate'] != null && $signatoryArray['birthDate'] != 'invalid date') {
                    $contact->setBirthDate(DateTime::createFromFormat("d/m/Y", $signatoryArray['birthDate']));
                }
                $contact->setPhoneNumber(($signatoryArray['phoneNumber'][0] == '0') ? substr($signatoryArray['phoneNumber'], 1) : $signatoryArray['phoneNumber']);
                $parsedPhone = $signatoryArray['phoneNumber'][0] == '0' ? substr($signatoryArray['phoneNumber'], 1) : $signatoryArray['phoneNumber'];
                $filterCode = (!isset($signatoryArray['codeCountry']) or $signatoryArray['codeCountry'] == null) ? '+33' : $signatoryArray['codeCountry'];
                $codeCountry = $this->franceTerritories($parsedPhone , $filterCode);
                $contact->setCodeCountry($codeCountry);
                $contact->setEmail($signatoryArray['email']);
                if (isset($signatoryArray['birthPlace']) && $signatoryArray['birthPlace'] != null) {
                    $contact->setBirthPlace($signatoryArray['birthPlace']);
                }
                if ($signatoryArray['role'] == 'enterprise') {
                    $contact->setEnterpriseName($signatoryArray['enterpriseName']);
                    $contact->setSiren($signatoryArray['siren']);
                    $nature='M';
                }
                if (sizeof($userAct) == 0) {
                    $actUser->setUser($signatory);
                    $actUser->setAct($act);
                }
                $contact->setModificationDate($date);
                $this->entityManager->persist($signatory);
                $this->entityManager->persist($actUser);
                $this->entityManager->persist($contact);

                $archive->setAct($act);
                $archive->setUser($this->security->getUser());
                $archive->setActor($this->security->getUser()->getLastName() . ' ' . $this->security->getUser()->getName());
                $archive->setActionDate($date);
                $archive->setAction('Action: Modification de l\'acte : Ajout de contact ' . $signatory->getName() . ' ' . $signatory->getLastName());

                $this->entityManager->persist($archive);
                $this->entityManager->flush();
                $this->logger->info('Adding a new signatory ' . substr_replace($contact->getName(), "*****", 3) . ' ' . substr_replace($contact->getLastName(), "*****", 3). ' email ' . substr_replace($contact->getEmail(), "*****", 3) . ' on act ' . ($act->getFolderNumber() ? $act->getFolderNumber() : $act->getId()) . ' by cnbId ' . $act->getInitiator()->getCnbId().' nature '.$nature);
                return $signatory->getId();
            } else {
                $signatoryContact->setContact($signatory);
                $signatoryContact->setInitiator($this->security->getUser());
                $actUser = new ActUser();
                $archive = new Archive();
                $userAct = $this->entityManager->getRepository(ActUser::class)->findBy(['user' => $signatory, 'act' => $act]);
                $signatoryContact->setLastname($signatoryArray['lastName']);
                $signatoryContact->setName($signatoryArray['name']);
                if (isset($signatoryArray['birthDate']) && $signatoryArray['birthDate'] != null && $signatoryArray['birthDate'] != 'invalid date') {
                    $signatoryContact->setBirthDate(DateTime::createFromFormat("d/m/Y", $signatoryArray['birthDate']));
                }
                $signatoryContact->setPhoneNumber(($signatoryArray['phoneNumber'][0] == '0') ? substr($signatoryArray['phoneNumber'], 1) : $signatoryArray['phoneNumber']);
                $parsedPhone = $signatoryArray['phoneNumber'][0] == '0' ? substr($signatoryArray['phoneNumber'], 1) : $signatoryArray['phoneNumber'];
                $filterCode = (!isset($signatoryArray['codeCountry']) or $signatoryArray['codeCountry'] == null) ? '+33' : $signatoryArray['codeCountry'];
                $codeCountry = $this->franceTerritories($parsedPhone , $filterCode);
                $signatoryContact->setCodeCountry($codeCountry);
                $signatoryContact->setEmail($signatoryArray['email']);
                if (isset($signatoryArray['birthPlace']) && $signatoryArray['birthPlace'] != null) {
                    $signatoryContact->setBirthPlace($signatoryArray['birthPlace']);
                }
                if ($signatoryArray['role'] == 'enterprise') {
                    $signatoryContact->setEnterpriseName($signatoryArray['enterpriseName']);
                    $signatoryContact->setSiren($signatoryArray['siren']);
                    $nature='M';
                }
                if (sizeof($userAct) == 0) {
                    $actUser->setUser($signatory);
                    $actUser->setAct($act);
                }
                $signatoryContact->setModificationDate($date);
                $this->entityManager->persist($signatory);
                $this->entityManager->persist($actUser);
                $this->entityManager->persist($signatoryContact);

                $archive->setAct($act);
                $archive->setUser($this->security->getUser());
                $archive->setActionDate($date);
                $archive->setActor($this->security->getUser()->getLastName() . ' ' . $this->security->getUser()->getName());
                $archive->setAction('Action: Modification de l\'acte : Ajout de contact ' . $signatory->getName() . ' ' . $signatory->getLastName());

                $this->entityManager->persist($archive);
                $this->entityManager->flush();
                $this->logger->info('Adding a new signatory from autoComplete ' .substr_replace( $signatoryContact->getName(), "*****", 3) . ' ' . substr_replace( $signatoryContact->getLastName(), "*****", 3). ' email ' . substr_replace($signatoryContact->getEmail(), "*****", 3) . ' on act ' . ($act->getFolderNumber() ? $act->getFolderNumber() : $act->getId()) . ' by cnbId ' . $act->getInitiator()->getCnbId().' nature '.$nature);

                return $signatory->getId();

            }
        }
        return false;
    }


    public function deleteSignatory($signatoryArray): bool
    {
        $act = $this->entityManager->getRepository(Act::class)->find($signatoryArray['actId']);
        if ($signatoryArray['role'] == 'enterprise') {
            $signatory = $this->entityManager->getRepository(Contact::class)->findOneBy(array('name' => $signatoryArray['name'], 'email' => $signatoryArray['email'], 'enterpriseName' => $signatoryArray['enterpriseName']));
        }else{
            $signatory = $this->entityManager->getRepository(Contact::class)->findOneBy(array('name' => $signatoryArray['name'], 'email' => $signatoryArray['email']));
        }
        $userAct = $this->entityManager->getRepository(ActUser::class)->findOneBy(array('user' => $signatory->getContact(), 'act' => $act));
        $this->logger->info('Delete signatory ' . substr_replace($signatory->getName(), "*****", 3). ' ' . substr_replace($signatory->getLastName(), "*****", 3). ' email ' . substr_replace($signatory->getEmail(), "*****", 3) . ' from act ' . ($act->getFolderNumber() ? $act->getFolderNumber() : $act->getId()) . ' by cnbId ' . $act->getInitiator()->getCnbId());
        $this->entityManager->remove($userAct);
        $date = new DateTime('@'.strtotime('now'));

        $archive = new Archive();
        $archive->setAct($act);
        $archive->setUser($act->getInitiator());
        $archive->setActor($act->getInitiator()->getLastName().' '.$act->getInitiator()->getName() );

        $archive->setAction('Action: Modification de l\'acte : Suppression du contact '.$signatory->getName().' '.$signatory->getLastName());
        $archive->setActionDate($date);
        $this->entityManager->persist($archive);
        $this->entityManager->flush();
        return true;
    }

    /**
     * @param $basicUserArray
     * @return bool
     */
    public function editBasicUser($basicUserArray): bool
    {
        $date = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);
        $basicUser = $this->entityManager->getRepository(Contact::class)->findOneBy(array('contact' => $basicUserArray['id'], 'initiator' => $this->security->getUser()));
        $basicUser->setLastname($basicUserArray['lastName']);
        if (isset($basicUserArray['birthDate']) && $basicUserArray['birthDate'] != null && $basicUserArray['birthDate'] != 'invalid date') {
            $basicUser->setBirthDate(DateTime::createFromFormat("d/m/Y", $basicUserArray['birthDate']));
        }
        $basicUser->setPhoneNumber(($basicUserArray['phoneNumber'][0] == '0') ? substr($basicUserArray['phoneNumber'], 1) : $basicUserArray['phoneNumber']);
        $basicUser->setEmail($basicUserArray['email']);
        if($basicUser->getContact()->getEmail()!=$basicUserArray['email'] && $basicUser->getContact()->getEmailApp()!=$basicUserArray['email']){
            $basicUser->setEmailApp($basicUserArray['email']);
        }
        if($basicUser->getContact()->getPhoneNumberApp()!=$basicUserArray['phoneNumber'] && $basicUser->getContact()->getPhoneNumber()!=$basicUserArray['phoneNumber']){
            $basicUser->setPhoneNumberApp(($basicUserArray['phoneNumber'][0] == '0') ? substr($basicUserArray['phoneNumber'], 1) : $basicUserArray['phoneNumber']);
            $basicUser->setCodeCountryApp((!isset($basicUserArray['codeCountry']) or $basicUserArray['codeCountry']==null) ?'+33':$basicUserArray['codeCountry']);
        }


        $basicUser->setCodeCountry((!isset($basicUserArray['codeCountry']) or $basicUserArray['codeCountry']==null) ?'+33':$basicUserArray['codeCountry']);
        if (isset($basicUserArray['birthPlace'])) {
            $basicUser->setBirthPlace($basicUserArray['birthPlace']);
        }
        if (array_key_exists('enterpriseName', $basicUserArray)) {
            $basicUser->setEnterpriseName($basicUserArray['enterpriseName']);
        }
        if (array_key_exists('siren',$basicUserArray)) {
            $basicUser->setSiren($basicUserArray['siren']);
        }
        $basicUser->setModificationDate($date);
        if($basicUser->getContact()->getRoles()[0] == 'ROLE_SIGNATORY'){
            $this->logger->info('Update signatory ' . substr_replace($basicUser->getName(), "*****", 3) . ' ' . substr_replace($basicUser->getLastName(), "*****", 3). ' email ' . substr_replace($basicUser->getEmail(), "*****", 3) . ' by cnbId ' . $this->security->getUser()->getCnbId());
        }else{
            $this->logger->info('Update lawyer ' . substr_replace($basicUser->getName(), "*****", 3) . ' ' . substr_replace($basicUser->getLastName(), "*****", 3). ' email ' . substr_replace($basicUser->getEmail(), "*****", 3) . ' by cnbId ' . $this->security->getUser()->getCnbId());

        }
        $this->entityManager->persist($basicUser);
        $this->entityManager->flush();
        return true;

    }

    /**
     * @param $signatoryArray
     * @param $user
     * @return User|bool|null|object
     */
    public function newContact($signatoryArray, $user)
    {
        $date = new \DateTime();
        $nature = 'P';
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);
        if ($signatoryArray['role'] == 'enterprise') {
            $signatory = $this->entityManager->getRepository(User::class)->findOneBy(['name' => $signatoryArray['name'], 'email' => $signatoryArray['email'], 'enterpriseName' => $signatoryArray['enterpriseName']]);
        }else{
            $signatory = $this->entityManager->getRepository(User::class)->findOneBy(['name' => $signatoryArray['name'], 'email' => $signatoryArray['email']]);
        }
        if (!$signatory) {
            $signatory = new User();
            $signatory->setName($signatoryArray['name']);
            if (isset($signatoryArray['enterpriseName']) and $signatoryArray['enterpriseName'] !== null ) {
                $signatory->setUsername($signatoryArray['email'].$signatoryArray['name'].$signatoryArray['lastName'].$signatoryArray['enterpriseName']);
            }else{
                $signatory->setUsername($signatoryArray['email'].$signatoryArray['name'].$signatoryArray['lastName']);
            }
            $signatory->setEnabled(false);
            $signatory->setLastname($signatoryArray['lastName']);
            $contact = new Contact();
            $contact->setContact($signatory);
            $contact->setInitiator($this->security->getUser());
            $contact->setLastname($signatoryArray['lastName']);
            $contact->setName($signatoryArray['name']);
            $contact->setPhoneNumber(($signatoryArray['phoneNumber'][0] == '0') ? substr($signatoryArray['phoneNumber'], 1) : $signatoryArray['phoneNumber']);
            $parsedPhone = $signatoryArray['phoneNumber'][0] == '0' ? substr($signatoryArray['phoneNumber'], 1) : $signatoryArray['phoneNumber'];
            $filterCode = (!isset($signatoryArray['codeCountry']) or $signatoryArray['codeCountry'] == null) ? '+33' : $signatoryArray['codeCountry'];
            $codeCountry = $this->franceTerritories($parsedPhone , $filterCode);
            $contact->setCodeCountry($codeCountry);
            $contact->setEmail($signatoryArray['email']);
            if (isset($signatoryArray['birthDate']) && $signatoryArray['birthDate'] != null && $signatoryArray['birthDate'] != 'invalid date') {
                $signatory->setBirthDate(DateTime::createFromFormat("d/m/Y",$signatoryArray['birthDate']));
                $contact->setBirthDate(DateTime::createFromFormat("d/m/Y",$signatoryArray['birthDate']));
            }
            $signatory->setPhoneNumber(($signatoryArray['phoneNumber'][0] == '0') ? substr($signatoryArray['phoneNumber'], 1) : $signatoryArray['phoneNumber']);
            $parsedPhone = $signatoryArray['phoneNumber'][0] == '0' ? substr($signatoryArray['phoneNumber'], 1) : $signatoryArray['phoneNumber'];
            $filterCode = (!isset($signatoryArray['codeCountry']) or $signatoryArray['codeCountry'] == null) ? '+33' : $signatoryArray['codeCountry'];
            $codeCountry = $this->franceTerritories($parsedPhone , $filterCode);
            $signatory->setCodeCountry($codeCountry);
            $signatory->setEmail($signatoryArray['email']);
            if (isset($signatoryArray['birthPlace']) && $signatoryArray['birthPlace'] != null) {
                $signatory->setBirthPlace($signatoryArray['birthPlace']);
                $contact->setBirthPlace($signatoryArray['birthPlace']);
            }
            $signatory->setPassword($signatoryArray['email']);
            $signatory->setCreatedBy($user);
            if ($signatoryArray['role'] == 'signatory') {
                $signatory->setRoles(['ROLE_SIGNATORY']);
            } else if ($signatoryArray['role'] == 'enterprise') {
                $signatory->setEnterpriseName($signatoryArray['enterpriseName']);
                $signatory->setSiren($signatoryArray['siren']);
                $contact->setEnterpriseName($signatoryArray['enterpriseName']);
                $contact->setSiren($signatoryArray['siren']);
                $signatory->setRoles(['ROLE_ENTERPRISE']);
                $nature = 'M';
            }
            $contact->setModificationDate($date);
            $this->entityManager->persist($signatory);
            $this->entityManager->persist($contact);
            $this->entityManager->flush();
            $this->logger->info('Create a new contact ' . substr_replace($contact->getName(), "*****", 3) . ' ' . substr_replace($contact->getLastName(), "*****", 3) . ' email ' . substr_replace($contact->getEmail(), "*****", 3) . ' by cnbId ' . $this->security->getUser()->getCnbId() . ' nature ' . $nature);
            return $this->entityManager->getRepository(User::class)->findUserById($contact->getId());
        } else {
            $signatoryContact = $this->entityManager->getRepository(Contact::class)->findOneBy(['email' => $signatoryArray['email'], 'name' => $signatoryArray['name'], 'initiator' => $this->security->getUser()]);
            if (!$signatoryContact) {
                $contact = new Contact();
                $contact->setContact($signatory);
                $contact->setInitiator($this->security->getUser());
                $contact->setLastname($signatoryArray['lastName']);
                $contact->setName($signatoryArray['name']);
                $contact->setPhoneNumber(($signatoryArray['phoneNumber'][0] == '0') ? substr($signatoryArray['phoneNumber'], 1) : $signatoryArray['phoneNumber']);
                $parsedPhone = $signatoryArray['phoneNumber'][0] == '0' ? substr($signatoryArray['phoneNumber'], 1) : $signatoryArray['phoneNumber'];
                $filterCode = (!isset($signatoryArray['codeCountry']) or $signatoryArray['codeCountry'] == null) ? '+33' : $signatoryArray['codeCountry'];
                $codeCountry = $this->franceTerritories($parsedPhone , $filterCode);
                $contact->setCodeCountry($codeCountry);
                $contact->setEmail($signatoryArray['email']);
                if (isset($signatoryArray['birthDate']) && $signatoryArray['birthDate'] != null && $signatoryArray['birthDate'] != 'invalid date') {
                    $contact->setBirthDate(DateTime::createFromFormat("d/m/Y",$signatoryArray['birthDate']));
                }
                if (isset($signatoryArray['birthPlace']) && $signatoryArray['birthPlace'] != null) {
                    $contact->setBirthPlace($signatoryArray['birthPlace']);
                }
                if ($signatoryArray['role'] == 'enterprise') {
                    $contact->setEnterpriseName($signatoryArray['enterpriseName']);
                    $contact->setSiren($signatoryArray['siren']);
                    $nature = 'P';
                }
                $contact->setModificationDate($date);
                $this->entityManager->persist($contact);
                $this->entityManager->flush();
                $this->logger->info('Create a new contact ' . substr_replace($contact->getName(), "*****", 3) . ' ' . substr_replace($contact->getLastName(), "*****", 3). ' email ' . substr_replace($contact->getEmail(), "*****", 3)  . ' by cnbId ' . $this->security->getUser()->getCnbId() . ' nature ' . $nature);
                return $this->entityManager->getRepository(User::class)->findUserById($contact->getId());
            }else{
                $contact = new Contact();
                $signatory = new User();
                $signatory->setEnabled(false);
                $signatory->setLastname($signatoryArray['lastName']);
                $signatory->setName($signatoryArray['name']);
                if (isset($signatoryArray['enterpriseName']) and $signatoryArray['enterpriseName'] !== null ) {
                    $signatory->setUsername($signatoryArray['email'].$signatoryArray['name'].$signatoryArray['lastName'].$signatoryArray['enterpriseName']);
                }else{
                    $signatory->setUsername($signatoryArray['email'].$signatoryArray['name'].$signatoryArray['lastName']);
                }
                $contact->setContact($signatory);
                $contact->setInitiator($this->security->getUser());
                $contact->setLastname($signatoryArray['lastName']);
                $contact->setName($signatoryArray['name']);
                $contact->setPhoneNumber(($signatoryArray['phoneNumber'][0] == '0') ? substr($signatoryArray['phoneNumber'], 1) : $signatoryArray['phoneNumber']);
                $parsedPhone = $signatoryArray['phoneNumber'][0] == '0' ? substr($signatoryArray['phoneNumber'], 1) : $signatoryArray['phoneNumber'];
                $filterCode = (!isset($signatoryArray['codeCountry']) or $signatoryArray['codeCountry'] == null) ? '+33' : $signatoryArray['codeCountry'];
                $codeCountry = $this->franceTerritories($parsedPhone , $filterCode);
                $contact->setCodeCountry($codeCountry);
                $contact->setEmail($signatoryArray['email']);
                if (isset($signatoryArray['birthDate']) && $signatoryArray['birthDate'] != null && $signatoryArray['birthDate'] != 'invalid date') {
                    $contact->setBirthDate(DateTime::createFromFormat("d/m/Y",$signatoryArray['birthDate']));
                }
                if (isset($signatoryArray['birthPlace']) && $signatoryArray['birthPlace'] != null) {
                    $signatory->setBirthDate(DateTime::createFromFormat("d/m/Y",$signatoryArray['birthDate']));
                    $contact->setBirthPlace($signatoryArray['birthPlace']);
                }
                $signatory->setPhoneNumber(($signatoryArray['phoneNumber'][0] == '0') ? substr($signatoryArray['phoneNumber'], 1) : $signatoryArray['phoneNumber']);
                $parsedPhone = $signatoryArray['phoneNumber'][0] == '0' ? substr($signatoryArray['phoneNumber'], 1) : $signatoryArray['phoneNumber'];
                $filterCode = (!isset($signatoryArray['codeCountry']) or $signatoryArray['codeCountry'] == null) ? '+33' : $signatoryArray['codeCountry'];
                $codeCountry = $this->franceTerritories($parsedPhone , $filterCode);
                $signatory->setCodeCountry($codeCountry);
                $signatory->setEmail($signatoryArray['email']);
                $signatory->setPassword($signatoryArray['email']);
                $signatory->setCreatedBy($user);
                if ($signatoryArray['role'] == 'enterprise') {
                    $signatory->setEnterpriseName($signatoryArray['enterpriseName']);
                    $signatory->setSiren($signatoryArray['siren']);
                    $signatory->setRoles(['ROLE_ENTERPRISE']);
                    $contact->setEnterpriseName($signatoryArray['enterpriseName']);
                    $contact->setSiren($signatoryArray['siren']);
                    $nature = 'P';
                }else{
                    $signatory->setRoles(['ROLE_SIGNATORY']);
                }
                $contact->setModificationDate($date);
                $this->entityManager->persist($contact);
                $this->entityManager->persist($signatory);
                $this->entityManager->flush();
                $this->logger->info('Create a new contact ' . substr_replace($contact->getName(), "*****", 3) . ' ' . substr_replace($contact->getLastName(), "*****", 3). ' email ' . substr_replace($contact->getEmail(), "*****", 3)  . ' by cnbId ' . $this->security->getUser()->getCnbId() . ' nature ' . $nature);
                return $this->entityManager->getRepository(User::class)->findUserById($contact->getId());
            }
            return false;
        }
    }

    /**
     * @param $signatoryArray
     * @return bool
     */
    public function updateContact($signatoryArray)
    {
        $date = new \DateTime();
        $nature = 'P';
        $timezone = new \DateTimeZone('Europe/Paris');
        $date->setTimezone($timezone);
        $signatory = $this->entityManager->getRepository(Contact::class)->findOneBy(array('contact' => $signatoryArray['id'], 'initiator' => $this->security->getUser()));
        if ($signatory) {
            $signatory->setName($signatoryArray['name']);
            $signatory->setLastname($signatoryArray['lastName']);
            if (isset($signatoryArray['birthDate']) && $signatoryArray['birthDate'] != null && $signatoryArray['birthDate'] != 'invalid date') {
                $signatory->setBirthDate(DateTime::createFromFormat("d/m/Y",$signatoryArray['birthDate']));
            }
            $signatory->setPhoneNumber(($signatoryArray['phoneNumber'][0] == '0') ? substr($signatoryArray['phoneNumber'], 1) : $signatoryArray['phoneNumber']);
            $parsedPhone = $signatoryArray['phoneNumber'][0] == '0' ? substr($signatoryArray['phoneNumber'], 1) : $signatoryArray['phoneNumber'];
            $filterCode = (!isset($signatoryArray['codeCountry']) or $signatoryArray['codeCountry'] == null) ? '+33' : $signatoryArray['codeCountry'];
            $codeCountry = $this->franceTerritories($parsedPhone , $filterCode);
            $signatory->setCodeCountry($codeCountry);
            $signatory->setEmail($signatoryArray['email']);
            if (isset($signatoryArray['birthPlace']) && $signatoryArray['birthPlace'] != null) {
                $signatory->setBirthPlace($signatoryArray['birthPlace']);
            }
            if (isset($signatoryArray['enterpriseName']) && $signatoryArray['enterpriseName'] != null) {
                $signatory->setEnterpriseName($signatoryArray['enterpriseName']);
                $nature = 'M';

            }
            if (isset($signatoryArray['siren']) && $signatoryArray['siren'] != null) {
                $signatory->setSiren($signatoryArray['siren']);
                $nature = 'M';
            }
            $signatory->setModificationDate($date);
            $this->entityManager->persist($signatory);
            $signatory->setVersion($signatory->getVersion() + 1);
            $this->entityManager->flush();
            $this->logger->info('Update contact signatory ' . substr_replace($signatory->getName(), "*****", 3) . ' ' . substr_replace($signatory->getLastName(), "*****", 3). ' email ' . substr_replace($signatory->getEmail(), "*****", 3) . ' by cnbId ' . $this->security->getUser()->getCnbId() . ' nature ' . $nature);
            return $this->entityManager->getRepository(User::class)->findUserById($signatory->getId());
        }
        return false;
    }
}