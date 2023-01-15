<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActUserRepository")
 */
class ActUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Many UserCompanies have One Company.
     * @ORM\ManyToOne(targetEntity="User", inversedBy="actUser", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true,onDelete="SET NULL")
     */
    private $user;

    /**
     * Many UserCompanies have One Company.
     * @ORM\ManyToOne(targetEntity="Act", inversedBy="actUser", cascade={"persist"})
     * @ORM\JoinColumn(name="act_id", referencedColumnName="id", nullable=true,onDelete="SET NULL")
     */
    private $act;

    /**
     * @ORM\Column(type="boolean",nullable=true)
     */
    private $validator;

    /**
     * @ORM\Column(type="boolean",nullable=true)
     */
    private $actValidated;

    /**
     * @ORM\Column(type="boolean",nullable=true)
     */
    private $mailSent;

    /**
     * @ORM\Column(type="text" , nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $validatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $signedAt;
    /**
     * @ORM\Column(type="text" , nullable=true)
     */
    private $signatureComment;

    /**
     * @ORM\Column(type="boolean" , nullable=true , options={"default" : 1})
     */
    private $enabled = true;
    /**
     * @ORM\Column(type="text" , nullable=true)
     */
    private $signatureId;
    /**
     * @ORM\Column(type="text" , nullable=true)
     */
    private $orderId;

    /**
     * @ORM\Column(type="boolean" , nullable=true)
     */
    private $signingInProgress;

    /**
     * @ORM\Column(type="string" , nullable=true)
     */
    private $otpCode;

    /**
     * @ORM\Column(type="datetime" , nullable=true)
     */
    private $signingTimeStamp;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }



    public function getValidator(): ?bool
    {
        return $this->validator;
    }

    public function setValidator(bool $validator): self
    {
        $this->validator = $validator;

        return $this;
    }

    public function getAct(): ?Act
    {
        return $this->act;
    }

    public function setAct(?Act $act): self
    {
        $this->act = $act;

        return $this;
    }

    public function getActValidated(): ?bool
    {
        return $this->actValidated;
    }

    public function setActValidated(?bool $actValidated): self
    {
        $this->actValidated = $actValidated;

        return $this;
    }

    public function getMailSent(): ?bool
    {
        return $this->mailSent;
    }

    public function setMailSent(?bool $mailSent): self
    {
        $this->mailSent = $mailSent;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getValidatedAt(): ?\DateTimeInterface
    {
        return $this->validatedAt;
    }

    public function setValidatedAt(?\DateTimeInterface $validatedAt): self
    {
        $this->validatedAt = $validatedAt;

        return $this;
    }

    public function getSignedAt(): ?\DateTimeInterface
    {
        return $this->signedAt;
    }

    public function setSignedAt(?\DateTimeInterface $signedAt): self
    {
        $this->signedAt = $signedAt;

        return $this;
    }

    public function getSignatureComment(): ?string
    {
        return $this->signatureComment;
    }

    public function setSignatureComment(?string $signatureComment): self
    {
        $this->signatureComment = $signatureComment;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getSignatureId(): ?string
    {
        return $this->signatureId;
    }

    public function setSignatureId(?string $signatureId): self
    {
        $this->signatureId = $signatureId;

        return $this;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function setOrderId(?string $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getSigningInProgress() : ?bool
    {
        return $this->signingInProgress;
    }

    public function setSigningInProgress( ?bool $signingInProgress): self
    {
        $this->signingInProgress = $signingInProgress;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOtpCode() : ?string
    {
        return $this->otpCode;
    }

    /**
     * @param mixed $otpCode
     */
    public function setOtpCode(?string $otpCode): self
    {
        $this->otpCode = $otpCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSigningTimeStamp() :?\DateTimeInterface
    {
        return $this->signingTimeStamp;
    }

    /**
     * @param mixed $signingTimeStamp
     */
    public function setSigningTimeStamp( ?\DateTimeInterface $signingTimeStamp): self
    {
        $this->signingTimeStamp = $signingTimeStamp;
        return $this;
    }
}
