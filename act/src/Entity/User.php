<?php
/**
 * Created by PhpStorm.
 * User: Helmi
 * Date: 12/10/2019
 * Time: 21:44
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @UniqueEntity(fields="usernameCanonical", errorPath="username", message="fos_user.username.already_used")
 * @ORM\AttributeOverrides({
 *      @ORM\AttributeOverride(name="email", column=@ORM\Column(type="string", name="email", length=255, unique=false, nullable=true)),
 *      @ORM\AttributeOverride(name="emailCanonical", column=@ORM\Column(type="string", name="email_canonical", length=255, unique=false, nullable=true))
 * })
 */
class User extends BaseUser implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $cnbId;

    /**
     * One Initiator has Many Acts.
     * @ORM\OneToMany(targetEntity="Act", mappedBy="initiator", cascade={"persist"})
     */
    private $act;
    /**
     * @ORM\OneToMany(targetEntity="Piwik", mappedBy="user", cascade={"persist"})
     */
    private $piwik;

    /**
     * @ORM\Column(type="string" , nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string" , nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string" , nullable=true)
     */
    private $phoneNumber;
    /**
     * @ORM\Column(type="string" , nullable=true)
     */
    private $codeCountry;

    /**
     * @ORM\Column(type="string" , nullable=true)
     */
    private $phoneNumberApp;
    /**
     * @ORM\Column(type="string" , nullable=true)
     */
    private $codeCountryApp;
    /**
     * @ORM\Column(type="string" , nullable=true)
     */
    private $emailApp;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $birthDate;

    /**
     * @ORM\Column(type="string" , nullable=true)
     */
    private $birthPlace;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $enterpriseName;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $edentitas = true;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $ipaddress;


    /**
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id",nullable=true)
     */
    private $createdBy;

    /**
     * One Product has Many Features.
     * @ORM\OneToMany(targetEntity="ActUser", mappedBy="user", cascade={"persist"})
     */
    private $actUser;
    /**
     * One Product has Many Features.
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="initiator", cascade={"persist"})
     */
    private $initiators;
    /**
     * One Product has Many Features.
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="contact", cascade={"persist"})
     */
    private $contacts;
    /**
     * @ORM\OneToMany(targetEntity="CguUser", mappedBy="user", cascade={"persist"})
     */
    private $cgu;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $resiliation;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $reason;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $scope;

    /**
     * One User has Many Archives.
     * @ORM\OneToMany(targetEntity="App\Entity\Archive", mappedBy="user", cascade={"persist"})
     */

    private $archives;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $siren;


    public function __construct()
    {
        parent::__construct();
        $this->act = new ArrayCollection();
        $this->refusals = new ArrayCollection();
        $this->acts = new ArrayCollection();
        $this->actUser = new ArrayCollection();
        // your own logic

        $this->initiators = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->cgu = new ArrayCollection();
        $this->piwik = new ArrayCollection();
        $this->archives = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Act[]
     */
    public function getAct(): Collection
    {
        return $this->act;
    }

    public function addAct(Act $act): self
    {
        if (!$this->act->contains($act)) {
            $this->act[] = $act;
            $act->setInitiator($this);
        }

        return $this;
    }

    public function removeAct(Act $act): self
    {
        if ($this->act->contains($act)) {
            $this->act->removeElement($act);
            // set the owning side to null (unless already changed)
            if ($act->getInitiator() === $this) {
                $act->setInitiator(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getBirthPlace(): ?string
    {
        return $this->birthPlace;
    }

    public function setBirthPlace(?string $birthPlace): self
    {
        $this->birthPlace = $birthPlace;

        return $this;
    }

    public function getEnterpriseName(): ?string
    {
        return $this->enterpriseName;
    }

    public function setEnterpriseName(?string $enterpriseName): self
    {
        $this->enterpriseName = $enterpriseName;

        return $this;
    }


    /**
     * @return Collection|Act[]
     */
    public function getActs(): Collection
    {
        return $this->acts;
    }

    public function getCreatedBy(): ?self
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?self $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return Collection|ActUser[]
     */
    public function getActUser(): Collection
    {
        return $this->actUser;
    }

    public function addActUser(ActUser $actUser): self
    {
        if (!$this->actUser->contains($actUser)) {
            $this->actUser[] = $actUser;
            $actUser->setUser($this);
        }

        return $this;
    }

    public function removeActUser(ActUser $actUser): self
    {
        if ($this->actUser->contains($actUser)) {
            $this->actUser->removeElement($actUser);
            // set the owning side to null (unless already changed)
            if ($actUser->getUser() === $this) {
                $actUser->setUser(null);
            }
        }

        return $this;
    }

//    public function setSamlAttributes(array $attributes)
//    {
//        $this->setUsername($attributes['cnb_prenom'][0].' '.$attributes['cnb_nom'][0]);
//        $this->setName($attributes['cnb_nom'][0]) ;
//        $this->setCnbId($attributes['cnb_id'][0]);
//        $this->setLastName($attributes['cnb_prenom'][0]);
//        $this->setEmail( $attributes['cnb_id'][0].'-cnb@cnb.fr');
//        $this->setPassword( "notused");
//
//    }


    public function getEdentitas(): ?bool
    {
        return $this->edentitas;
    }

    public function setEdentitas(?bool $edentitas): self
    {
        $this->edentitas = $edentitas;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCnbId()
    {
        return $this->cnbId;
    }

    /**
     * @param mixed $cnbId
     */
    public function setCnbId($cnbId): void
    {
        $this->cnbId = $cnbId;
    }

    public function getIpaddress(): ?string
    {
        return $this->ipaddress;
    }

    public function setIpaddress(?string $ipaddress): self
    {
        $this->ipaddress = $ipaddress;

        return $this;
    }

    /**
     * @return Collection|Contact[]
     */
    public function getInitiators(): Collection
    {
        return $this->initiators;
    }

    public function addInitiator(Contact $initiator): self
    {
        if (!$this->initiators->contains($initiator)) {
            $this->initiators[] = $initiator;
            $initiator->setInitiator($this);
        }

        return $this;
    }

    public function removeInitiator(Contact $initiator): self
    {
        if ($this->initiators->contains($initiator)) {
            $this->initiators->removeElement($initiator);
            // set the owning side to null (unless already changed)
            if ($initiator->getInitiator() === $this) {
                $initiator->setInitiator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Contact[]
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setInitiator($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->contains($contact)) {
            $this->contacts->removeElement($contact);
            // set the owning side to null (unless already changed)
            if ($contact->getInitiator() === $this) {
                $contact->setInitiator(null);
            }
        }

        return $this;
    }

    public function getPhoneNumberApp(): ?string
    {
        return $this->phoneNumberApp;
    }

    public function setPhoneNumberApp(?string $phoneNumberApp): self
    {
        $this->phoneNumberApp = $phoneNumberApp;

        return $this;
    }

    public function getEmailApp(): ?string
    {
        return $this->emailApp;
    }

    public function setEmailApp(?string $emailApp): self
    {
        $this->emailApp = $emailApp;

        return $this;
    }

    /**
     * @return Collection|CguUser[]
     */
    public function getCgu(): Collection
    {
        return $this->cgu;
    }

    public function addCgu(CguUser $cgu): self
    {
        if (!$this->cgu->contains($cgu)) {
            $this->cgu[] = $cgu;
            $cgu->setUser($this);
        }

        return $this;
    }

    public function removeCgu(CguUser $cgu): self
    {
        if ($this->cgu->contains($cgu)) {
            $this->cgu->removeElement($cgu);
            // set the owning side to null (unless already changed)
            if ($cgu->getUser() === $this) {
                $cgu->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Piwik[]
     */
    public function getPiwik(): Collection
    {
        return $this->piwik;
    }

    public function addPiwik(Piwik $piwik): self
    {
        if (!$this->piwik->contains($piwik)) {
            $this->piwik[] = $piwik;
            $piwik->setUser($this);
        }

        return $this;
    }

    public function removePiwik(Piwik $piwik): self
    {
        if ($this->piwik->contains($piwik)) {
            $this->piwik->removeElement($piwik);
            // set the owning side to null (unless already changed)
            if ($piwik->getUser() === $this) {
                $piwik->setUser(null);
            }
        }

        return $this;
    }

    public function getCodeCountry(): ?string
    {
        return $this->codeCountry;
    }

    public function setCodeCountry(?string $codeCountry): self
    {
        $this->codeCountry = $codeCountry;

        return $this;
    }

    public function getCodeCountryApp(): ?string
    {
        return $this->codeCountryApp;
    }

    public function setCodeCountryApp(?string $codeCountryApp): self
    {
        $this->codeCountryApp = $codeCountryApp;

        return $this;
    }

    public function getResiliation(): ?\DateTimeInterface
    {
        return $this->resiliation;
    }

    public function setResiliation(?\DateTimeInterface $resiliation): self
    {
        $this->resiliation = $resiliation;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getArchives()
    {
        return $this->archives;
    }

    /**
     * @param mixed $archives
     */
    public function setArchives($archives): void
    {
        $this->archives = $archives;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function setScope(?string $scope): self
    {
        $this->scope = $scope;

        return $this;
    }

    public function addArchive(Archive $archive): self
    {
        if (!$this->archives->contains($archive)) {
            $this->archives[] = $archive;
            $archive->setUser($this);
        }

        return $this;
    }

    public function removeArchive(Archive $archive): self
    {
        if ($this->archives->contains($archive)) {
            $this->archives->removeElement($archive);
            // set the owning side to null (unless already changed)
            if ($archive->getUser() === $this) {
                $archive->setUser(null);
            }
        }

        return $this;
    }

    public function getSiren():?string
    {
        return $this->siren;
    }

    public function setSiren(?string $siren):self  
    {
       $this->siren = $siren;
       return $this;
    }


}