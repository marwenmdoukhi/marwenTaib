<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArchiveRepository")
 */
class Archive
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $action;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $actionDate;

    /**
     * Many Archives have One Act.
     * @ORM\ManyToOne(targetEntity="Act", inversedBy="archives", cascade={"persist"})
     * @ORM\JoinColumn(name="act_id", referencedColumnName="id", nullable=true,onDelete="SET NULL")
     */
    private $act;

    /**
     * Many Archives have One User.
     * @ORM\ManyToOne(targetEntity="User", inversedBy="archives", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true,onDelete="SET NULL")
     */
    private $user;

    /**
     * @ORM\Column(type="string" , nullable=true)
     */
    private $actor;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getActionDate(): ?\DateTimeInterface
    {
        return $this->actionDate;
    }

    public function setActionDate(?\DateTimeInterface $actionDate): self
    {
        $this->actionDate = $actionDate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAct()
    {
        return $this->act;
    }

    /**
     * @param mixed $act
     */
    public function setAct($act): void
    {
        $this->act = $act;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @param mixed $actor
     */
    public function setActor($actor): void
    {
        $this->actor = $actor;
    }
}
