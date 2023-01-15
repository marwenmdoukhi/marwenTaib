<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=30)
     */
    private $name;

    /**
     * @ORM\OneToMany (targetEntity=Product::class, mappedBy="categories")
     */
    private $products;


    /**
     * @ORM\Column(type="string", length=256)
     */
    private $slug;



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Subcategory", mappedBy="category")
     */
    private $subCategories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Marque", mappedBy="category")
     */
    private $marque;

    /**
     * @ORM\OneToMany (targetEntity="App\Entity\Style",mappedBy="category")
     */
    private $style;

    /**
     * @ORM\OneToMany (targetEntity="App\Entity\Forme",mappedBy="category")
     */
    private $forme;

    /**
     * @ORM\OneToMany (targetEntity="App\Entity\Cadre",mappedBy="category")
     */
    private $cadres;

    /**
     * @ORM\OneToMany (targetEntity="App\Entity\MatierBracelet",mappedBy="category")
     */
    private $matierBracelet;

    /**
     * @ORM\OneToMany (targetEntity="App\Entity\TypeDuMouvement",mappedBy="category")
     */
    private $typeDuMouvement;

    /**
     * @ORM\OneToMany (targetEntity="App\Entity\FormeDuCadran",mappedBy="category")
     */
    private $formeDuCadran;

    /**
     * @ORM\OneToMany(targetEntity=Volume::class, mappedBy="category")
     */
    private $volumes;

    /**
     * @ORM\OneToMany(targetEntity=TypeDeMaquillage::class, mappedBy="category")
     */
    private $typeDeMaquillages;

    /**
     * @ORM\Column(type="boolean", options={"default":"1"})
     */
    private $activer=true;

    /**
     * @ORM\Column(type="string", length=256,nullable=true)
     */
    private $picture;


    public function __toString()
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->subCategories = new ArrayCollection();
        $this->marque = new ArrayCollection();
        $this->style = new ArrayCollection();
        $this->forme = new ArrayCollection();
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
            $product->setCategories($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCategories() === $this) {
                $product->setCategories(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Subcategory[]
     */
    public function getSubCategories(): Collection
    {
        return $this->subCategories;
    }

    public function addSubCategory(Subcategory $subCategory): self
    {
        if (!$this->subCategories->contains($subCategory)) {
            $this->subCategories[] = $subCategory;
            $subCategory->setCategory($this);
        }

        return $this;
    }

    public function removeSubCategory(Subcategory $subCategory): self
    {
        if ($this->subCategories->removeElement($subCategory)) {
            // set the owning side to null (unless already changed)
            if ($subCategory->getCategory() === $this) {
                $subCategory->setCategory(null);
            }
        }

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
     * @return ArrayCollection
     */
    public function getMarque()
    {
        return $this->marque;
    }

    /**
     * @param $marque
     * @return $this|null
     */
    public function setMarque($marque): ?Category
    {
        $this->marque = $marque;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param Style|null $style
     * @return $this
     */
    public function setStyle(?Style $style): Category
    {
        $this->style = $style;
        return  $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getForme()
    {
        return $this->forme;
    }

    /**
     * @param mixed $forme
     */
    public function setForme(?Forme  $forme): Category
    {
        $this->forme = $forme;
        return $this;
    }

    /**
     * @return Category|null
     */
    public function getCadres(): ?Category
    {
        return $this->cadres;
    }

    /**
     * @param Cadre|null $cadres
     * @return $this
     */
    public function setCadres(?Cadre  $cadres): Category
    {
        $this->cadres = $cadres;
        return $this;
    }

    /**
     * @return MatierBracelet|null
     */
    public function getMatierBracelet(): ?MatierBracelet
    {
        return $this->matierBracelet;
    }

    /**
     * @param MatierBracelet|null $matierBracelet
     * @return $this
     */
    public function setMatierBracelet(?MatierBracelet $matierBracelet): Category
    {
        $this->matierBracelet = $matierBracelet;
        return $this;
    }

    /**
     * @return TypeDuMouvement|null
     */
    public function getTypeDuMouvement(): ?TypeDuMouvement
    {
        return $this->typeDuMouvement;
    }

    /**
     * @param TypeDuMouvement|null $typeDuMouvement
     * @return Category
     */
    public function setTypeDuMouvement(?TypeDuMouvement $typeDuMouvement): Category
    {
        $this->typeDuMouvement = $typeDuMouvement;
        return $this;
    }

    /**
     * @return FormeDuCadran|null
     */
    public function getFormeDuCadran(): ?FormeDuCadran
    {
        return $this->formeDuCadran;
    }

    /**
     * @param FormeDuCadran|null $formeDuCadran
     * @return $this
     */
    public function setFormeDuCadran(?FormeDuCadran  $formeDuCadran): Category
    {
        $this->formeDuCadran = $formeDuCadran;
        return  $this;
    }

    /**
     * @return Volume|null
     */
    public function getVolumes(): ?Volume
    {
        return $this->volumes;
    }

    /**
     * @param Volume|null $volumes
     * @return $this
     */
    public function setVolumes(?Volume $volumes): Category
    {
        $this->volumes = $volumes;
        return $this;
    }

    /**
     * @return TypeDeMaquillage|null
     */
    public function getTypeDeMaquillages(): ?TypeDeMaquillage
    {
        return $this->typeDeMaquillages;
    }

    /**
     * @param TypeDeMaquillage|null $typeDeMaquillages
     * @return Category
     */
    public function setTypeDeMaquillages(?TypeDeMaquillage $typeDeMaquillages): Category
    {
        $this->typeDeMaquillages = $typeDeMaquillages;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getActiver()
    {
        return $this->activer;
    }

    /**
     * @param mixed $activer
     */
    public function setActiver($activer): void
    {
        $this->activer = $activer;
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
     * @return Category
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
        return $this;
    }
}
