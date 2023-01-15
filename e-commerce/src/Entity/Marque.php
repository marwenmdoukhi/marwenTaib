<?php

namespace App\Entity;

use App\Repository\MarqueRepository;
use Cocur\Slugify\Slugify;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MarqueRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Marque
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
     * @ORM\Column(type="string", length=256)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="marque")
     */
    private $products;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="marque")
     */
    private $category;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): ?Marque
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array
     */
    public function getProduits()
    {
        return $this->produits;
    }

    /**
     * @param Product $produit
     * @return $this|null
     */
    public function addProduit(Product $produit): ?Marque
    {
        if (!$this->produits->contains($produit)) {
            $this->produits[] = $produit;
            $produit->setMarque($this);
        }

        return $this;
    }

    /**
     * @param Product $produit
     * @return $this|null
     */
    public function removeProduit(Product $produit): ?Marque
    {
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getMarque() === $this) {
                $produit->setMarque(null);
            }
        }
        return $this;
    }


    /**
     * @return Collection|self[]
     */
    public function getMarques(): Collection
    {
        return $this->marques;
    }

    public function addMarque(self $marque): self
    {
        if (!$this->marques->contains($marque)) {
            $this->marques[] = $marque;
            $marque->addRelation($this);
        }

        return $this;
    }

    public function removeMarque(self $marque): self
    {
        if ($this->marques->removeElement($marque)) {
            $marque->removeRelation($this);
        }

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setMarque($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getMarque() === $this) {
                $product->setMarque(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }



    /**
     * @param mixed $slug
     * @return Marque
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function initialSlug()
    {
        if (empty($this->getSlug())) {
            $slugify = new Slugify();
            $this->slug=$slugify->slugify($this->name);
        }
    }

    /**
     * @return mixed
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category|null $category
     * @return $this
     */
    public function setCategory(?Category $category): Marque
    {
        $this->category = $category;
        return  $this;
    }
}
