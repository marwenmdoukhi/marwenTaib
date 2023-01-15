<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContactRepository")
 */
class Contact
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Many UserCompanies have One Company.
     * @ORM\ManyToOne(targetEntity="User", inversedBy="initiators", cascade={"persist"})
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=true,onDelete="SET NULL")
     */
    private $initiator;

    /**
     * Many UserCompanies have One Company.
     * @ORM\ManyToOne(targetEntity="User", inversedBy="contacts", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true,onDelete="SET NULL")
     */
    private $contact;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $phoneNumber;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $codeCountry;
    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $cnbId;
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
    private $phoneNumberApp;
    /**
     * @ORM\Column(type="string", nullable=true)
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
     * @ORM\Column(type="integer" , options={"default" : 0})
     */
    private $version=0;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $enterpriseName;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $modificationDate;
    /**
     * @ORM\OneToMany(targetEntity="Proof", mappedBy="contact", cascade={"persist"})
     */
    private $proof;

     /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $siren;

    public function __construct()
    {
        $this->proof = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInitiator(): ?User
    {
        return $this->initiator;
    }

    public function setInitiator(?User $initiator): self
    {
        $this->initiator = $initiator;

        return $this;
    }

    public function getContact(): ?User
    {
        return $this->contact;
    }

    public function setContact(?User $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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

    public function getCnbId(): ?int
    {
        return $this->cnbId;
    }

    public function setCnbId(?int $cnbId): self
    {
        $this->cnbId = $cnbId;

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

    public function getModificationDate(): ?\DateTimeInterface
    {
        return $this->modificationDate;
    }

    public function setModificationDate(?\DateTimeInterface $modificationDate): self
    {
        $this->modificationDate = $modificationDate;

        return $this;
    }

    /**
     * @return Collection|Proof[]
     */
    public function getProof(): Collection
    {
        return $this->proof;
    }

    public function addProof(Proof $proof): self
    {
        if (!$this->proof->contains($proof)) {
            $this->proof[] = $proof;
            $proof->setContact($this);
        }

        return $this;
    }

    public function removeProof(Proof $proof): self
    {
        if ($this->proof->contains($proof)) {
            $this->proof->removeElement($proof);
            // set the owning side to null (unless already changed)
            if ($proof->getContact() === $this) {
                $proof->setContact(null);
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

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(?int $version): self
    {
        $this->version = $version;

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
