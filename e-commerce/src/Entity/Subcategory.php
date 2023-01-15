<?php

namespace App\Entity;

use App\Repository\SubcategoryRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubcategoryRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Subcategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $name;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="subCategory")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="subCategories")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $slug;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\SubSubCategories", mappedBy="subCategories")
     */
    private $subSubCategories;

    /**
     * @ORM\Column(type="string", length=256,nullable=true)
     */
    private $picture;

    public function __construct()
    {
        $this->product = new ArrayCollection();
        $this->subSubCategories = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Category
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category|null $category
     * @return Category
     */
    public function setCategory(?Category $category): Subcategory
    {
        $this->category = $category;
        return $this;
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

    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->product;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->product->contains($product)) {
            $this->product[] = $product;
            $product->setSubCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->product->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getSubCategory() === $this) {
                $product->setSubCategory(null);
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
     */
    public function setSlug($slug): void
    {
        $this->slug = $slug;
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
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param ArrayCollection $product
     * @return $this|null
     */
    public function setProduct(ArrayCollection $product): ?Subcategory
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubSubCategories()
    {
        return $this->subSubCategories;
    }

    /**
     * @param SubSubCategories $subSubCategories
     * @return $this|null
     */
    public function setSubSubCategories(SubSubCategories $subSubCategories): ?Subcategory
    {
        $this->subSubCategories = $subSubCategories;
        return $this;
    }

    public function addSubSubCategory(SubSubCategories $subSubCategory): self
    {
        if (!$this->subSubCategories->contains($subSubCategory)) {
            $this->subSubCategories[] = $subSubCategory;
            $subSubCategory->addSubCategory($this);
        }

        return $this;
    }

    public function removeSubSubCategory(SubSubCategories $subSubCategory): self
    {
        if ($this->subSubCategories->removeElement($subSubCategory)) {
            $subSubCategory->removeSubCategory($this);
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
     * @return Subcategory
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
        return $this;
    }
}
