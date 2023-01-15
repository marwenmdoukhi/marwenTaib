<?php

namespace App\Entity;

use App\Repository\SubSubCategoriesRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubSubCategoriesRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class SubSubCategories
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Subcategory", inversedBy="subSubCategories")
     * @ORM\JoinColumn(name="sub_categories", referencedColumnName="id",nullable=true)
     */
    private $subCategories;


    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="subSubCategories")
     * @ORM\JoinColumn(name="produit_id", referencedColumnName="id",nullable=true)
     */
    private $products;

    /**
     * @ORM\Column(type="string", length=256,nullable=true)
     */
    private $picture;

    public function __construct()
    {
        $this->subCategories = new ArrayCollection();
        $this->products = new ArrayCollection();
    }


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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSubCategories()
    {
        return $this->subCategories;
    }

    /**
     * @param Subcategory|null $subCategories
     * @return $this
     */
    public function setSubCategories(?Subcategory $subCategories): SubSubCategories
    {
        $this->subCategories = $subCategories;
        return $this;
    }

    /**
     * @return SubSubCategories|null
     */
    public function getProducts(): ?SubSubCategories
    {
        return $this->products;
    }

    /**
     * @param Product|null $products
     * @return $this
     */
    public function setProducts(?Product $products): SubSubCategories
    {
        $this->products = $products;
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

    public function addSubCategory(Subcategory $subCategory): self
    {
        if (!$this->subCategories->contains($subCategory)) {
            $this->subCategories[] = $subCategory;
        }

        return $this;
    }

    public function removeSubCategory(Subcategory $subCategory): self
    {
        $this->subCategories->removeElement($subCategory);

        return $this;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setSubSubCategories($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getSubSubCategories() === $this) {
                $product->setSubSubCategories(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param mixed $picture
     */
    public function setPicture($picture): void
    {
        $this->picture = $picture;
    }
}
