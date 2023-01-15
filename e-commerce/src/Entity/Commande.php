<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 *
 */
class Commande
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $Numero;

    /**
     * @ORM\Column(type="float")
     */
    private $Montant;

    /**
     * @ORM\Column(type="datetime")
     */
    private $CreatedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CommandeArticle", mappedBy="Commande",cascade={"persist", "remove"})
     */
    private $commandeArticles;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $CartCmd = [];

    /**
     *@ManyToOne(targetEntity="App\Entity\User", inversedBy="Commande",cascade={"persist"})
     */
    public $users;


    /**
     * @var string
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @var boolean
     * @ORM\Column(name="terminer", type="boolean")
     */
    private $terminer;

    /**
     * @var boolean
     * @ORM\Column(name="payer", type="boolean",nullable=true)
     */
    private $payer;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $datedepaimenet;

    public function __construct()
    {
        $this->commandeArticles = new ArrayCollection();
        $this->Numero= ('WFDALI'.(uniqid(random_int(1, 99))));
        $this->setCreatedAt(new \DateTime('1 hour'));
        $this->terminer=false;
        $this->status=false;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNumero(): string
    {
        return $this->Numero;
    }

    /**
     * @param string $Numero
     */
    public function setNumero(string $Numero)
    {
        $this->Numero = $Numero;
    }



    public function getMontant(): ?float
    {
        return $this->Montant;
    }

    public function setMontant(float $Montant): self
    {
        $this->Montant = $Montant;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->CreatedAt;
    }

    public function setCreatedAt(\DateTimeInterface $CreatedAt): self
    {
        $this->CreatedAt = $CreatedAt;

        return $this;
    }

    /**
     * @return Collection|CommandeArticle[]
     */
    public function getCommandeArticles(): Collection
    {
        return $this->commandeArticles;
    }

    public function addCommandeArticle(CommandeArticle $commandeArticle): self
    {
        if (!$this->commandeArticles->contains($commandeArticle)) {
            $this->commandeArticles[] = $commandeArticle;
            $commandeArticle->setCommande($this);
        }

        return $this;
    }

    public function removeCommandeArticle(CommandeArticle $commandeArticle): self
    {
        if ($this->commandeArticles->contains($commandeArticle)) {
            $this->commandeArticles->removeElement($commandeArticle);
            // set the owning side to null (unless already changed)
            if ($commandeArticle->getCommande() === $this) {
                $commandeArticle->setCommande(null);
            }
        }

        return $this;
    }

    public function getCartCmd(): ?array
    {
        return $this->CartCmd;
    }

    public function setCartCmd(?array $CartCmd): self
    {
        $this->CartCmd = $CartCmd;

        return $this;
    }

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): self
    {
        $this->users = $users;

        return $this;
    }



    /**
     * @return bool
     */
    public function isTerminer(): bool
    {
        return $this->terminer;
    }

    /**
     * @param bool $terminer
     */
    public function setTerminer(bool $terminer): void
    {
        $this->terminer = $terminer;
    }








    /**
     * @return mixed
     */
    public function getDatedepaimenet()
    {
        return $this->datedepaimenet;
    }

    /**
     * @param mixed $datedepaimenet
     * @return Commande
     */
    public function setDatedepaimenet($datedepaimenet)
    {
        $this->datedepaimenet = $datedepaimenet;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Commande
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPayer(): bool
    {
        return $this->payer;
    }

    /**
     * @param bool $payer
     * @return Commande
     */
    public function setPayer(bool $payer): Commande
    {
        $this->payer = $payer;
        return $this;
    }

    public function getTerminer(): ?bool
    {
        return $this->terminer;
    }

    public function getPayer(): ?bool
    {
        return $this->payer;
    }
}
