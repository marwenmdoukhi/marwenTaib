<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProofRepository")
 */
class Proof
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $event;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $ipaddress;
    /**
     * @ORM\ManyToOne(targetEntity="Act", inversedBy="proof", cascade={"persist"})
     * @ORM\JoinColumn(name="act_id", referencedColumnName="id", nullable=true,onDelete="SET NULL")
     */
    private $act;
    /**
     * @ORM\ManyToOne(targetEntity="Contact", inversedBy="proof", cascade={"persist"})
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id", nullable=true,onDelete="SET NULL")
     */
    private $contact;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    public function setEvent(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self
    {
        $this->contact = $contact;

        return $this;
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
}
