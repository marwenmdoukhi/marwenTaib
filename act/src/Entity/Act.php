<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActRepository")
 */
class Act
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $internalNumber;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $requestDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $signingDate;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $receptionDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $folderName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="integer", length=255, nullable=true)
     */
    private $orderRequestId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $folderNumber;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastResentDate;

    /**
     * @ORM\Column(type="integer" , length=255 , nullable=true)
     */
    private $actPdfPagesNumber;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expirationDate;

    /**
     * Many Acts have One Initiator.
     * @ORM\ManyToOne(targetEntity="User", inversedBy="act", cascade={"persist"})
     * @ORM\JoinColumn(name="initiator_id", referencedColumnName="id", nullable=true,onDelete="SET NULL")
     */
    private $initiator;

    /**
     * One Act has Many Documents.
     * @ORM\OneToMany(targetEntity="Document", mappedBy="act", cascade={"persist"})
     */
    private $documents;
    /**
     * @ORM\OneToMany(targetEntity="Proof", mappedBy="act", cascade={"persist"})
     */
    private $proof;

    /**
     * One Product has Many Features.
     * @ORM\OneToMany(targetEntity="ActUser", mappedBy="act", cascade={"persist"})
     */
    private $actUser;

    /**
     * One Act has Many Archives.
     * @ORM\OneToMany(targetEntity="App\Entity\Archive", mappedBy="act", cascade={"persist"})
     */
    private $archives;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->refusals = new ArrayCollection();
        $this->eventStatus = new ArrayCollection();
        $this->basicUsers = new ArrayCollection();
        $this->actUser = new ArrayCollection();
        $this->proof = new ArrayCollection();
        $this->archives = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getInternalNumber(): ?string
    {
        return $this->internalNumber;
    }

    public function setInternalNumber(?string $internalNumber): self
    {
        $this->internalNumber = $internalNumber;

        return $this;
    }

    public function getRequestDate(): ?\DateTimeInterface
    {
        return $this->requestDate;
    }

    public function setRequestDate(?\DateTimeInterface $requestDate): self
    {
        $this->requestDate = $requestDate;

        return $this;
    }

    public function getFolderName(): ?string
    {
        return $this->folderName;
    }

    public function setFolderName(?string $folderName): self
    {
        $this->folderName = $folderName;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getOrderRequestId(): ?int
    {
        return $this->orderRequestId;
    }

    public function setOrderRequestId(?int $orderRequestId): self
    {
        $this->orderRequestId = $orderRequestId;

        return $this;
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

    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setAct($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            // set the owning side to null (unless already changed)
            if ($document->getAct() === $this) {
                $document->setAct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getBasicUsers(): Collection
    {
        return $this->basicUsers;
    }

    public function addBasicUser(User $basicUser): self
    {
        if (!$this->basicUsers->contains($basicUser)) {
            $this->basicUsers[] = $basicUser;
            $basicUser->addAct($this);
        }

        return $this;
    }

    public function removeBasicUser(User $basicUser): self
    {
        if ($this->basicUsers->contains($basicUser)) {
            $this->basicUsers->removeElement($basicUser);
            $basicUser->removeAct($this);
        }

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
            $actUser->setAct($this);
        }

        return $this;
    }

    public function removeActUser(ActUser $actUser): self
    {
        if ($this->actUser->contains($actUser)) {
            $this->actUser->removeElement($actUser);
            // set the owning side to null (unless already changed)
            if ($actUser->getAct() === $this) {
                $actUser->setAct(null);
            }
        }

        return $this;
    }

    public function getFolderNumber(): ?string
    {
        return $this->folderNumber;
    }

    public function setFolderNumber(?string $folderNumber): self
    {
        $this->folderNumber = $folderNumber;

        return $this;
    }

    public function getSigningDate(): ?\DateTimeInterface
    {
        return $this->signingDate;
    }

    public function setSigningDate(?\DateTimeInterface $signingDate): self
    {
        $this->signingDate = $signingDate;

        return $this;
    }

    public function getLastResentDate(): ?\DateTimeInterface
    {
        return $this->lastResentDate;
    }

    public function setLastResentDate(?\DateTimeInterface $lastResentDate): self
    {
        $this->lastResentDate = $lastResentDate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getReceptionDate()
    {
        return $this->receptionDate;
    }

    /**
     * @param mixed $receptionDate
     */
    public function setReceptionDate($receptionDate): void
    {
        $this->receptionDate = $receptionDate;
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
            $proof->setAct($this);
        }

        return $this;
    }

    public function removeProof(Proof $proof): self
    {
        if ($this->proof->contains($proof)) {
            $this->proof->removeElement($proof);
            // set the owning side to null (unless already changed)
            if ($proof->getAct() === $this) {
                $proof->setAct(null);
            }
        }

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

    public function addArchive(Archive $archive): self
    {
        if (!$this->archives->contains($archive)) {
            $this->archives[] = $archive;
            $archive->setAct($this);
        }

        return $this;
    }

    public function removeArchive(Archive $archive): self
    {
        if ($this->archives->contains($archive)) {
            $this->archives->removeElement($archive);
            // set the owning side to null (unless already changed)
            if ($archive->getAct() === $this) {
                $archive->setAct(null);
            }
        }

        return $this;
    }


    public function getActPdfPagesNumber() : ?int
    {
        return $this->actPdfPagesNumber;
    }


    public function setActPdfPagesNumber(?int $actPdfPagesNumber): self
    {
        $this->actPdfPagesNumber = $actPdfPagesNumber;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @param mixed $expirationDate
     */
    public function setExpirationDate($expirationDate): self
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }
}
