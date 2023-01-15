<?php

namespace App\Entity;

use App\Repository\FragranceDeParfumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FragranceDeParfumRepository::class)
 */
class FragranceDeParfum
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
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="fragranceDeParfum")
     */
    private $products;

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
     * @return FragranceDeParfum
     */
    public function setProducts(?Product  $products): FragranceDeParfum
    {
        $this->products = $products;
        return $this;
    }
}
