<?php

namespace App\Entity;

use App\Repository\FormeDuCadranRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FormeDuCadranRepository::class)
 */
class FormeDuCadran
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="formeDuCadran")
     * @ORM\JoinColumn(name="produit_id", referencedColumnName="id",nullable=true)
     */
    private $products;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="formeDuCadran")
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
     * @return Product|null
     */
    public function getProducts(): ?Product
    {
        return $this->products;
    }

    /**
     * @param Product|null $products
     * @return $this
     */
    public function setProducts(?Product  $products): FormeDuCadran
    {
        $this->products = $products;
        return $this;
    }

    /**
     * @return FormeDuCadran|null
     */
    public function getCategory(): ?FormeDuCadran
    {
        return $this->category;
    }

    /**
     * @param Category|null $category
     * @return $this
     */
    public function setCategory(?Category  $category): FormeDuCadran
    {
        $this->category = $category;
        return $this;
    }
}
