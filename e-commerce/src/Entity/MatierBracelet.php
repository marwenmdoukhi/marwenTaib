<?php

namespace App\Entity;

use App\Repository\MatierBraceletRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MatierBraceletRepository::class)
 */
class MatierBracelet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="matierBracelet")
     * @ORM\JoinColumn(name="produit_id", referencedColumnName="id",nullable=true)
     */
    private $products;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="matierBracelet")
     */
    private $category;


    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Product|ArrayCollection|null
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param Product|null $products
     * @return $this
     */
    public function setProducts(?Product  $products): MatierBracelet
    {
        $this->products = $products;
        return $this;
    }

    /**
     * @return MatierBracelet|null
     */
    public function getCategory(): ?MatierBracelet
    {
        return $this->category;
    }

    /**
     * @param Category|null $category
     * @return $this
     */
    public function setCategory(?Category  $category): MatierBracelet
    {
        $this->category = $category;
        return $this;
    }
}
