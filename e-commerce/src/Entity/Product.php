<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Cocur\Slugify\Slugify;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks()
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var string |null
     * @ORM\Column(type="string", length=256, nullable=true)
     *
     */
    private $filename;

    /**
     * @var File |null
     * @Vich\UploadableField(mapping="product_images", fileNameProperty="filename")
     *
     */
    private $imageFile;

    /**
     * @ORM\Column(type="boolean")
     */
    private $promo;

    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $pricePromo=0;

    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $newprice;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime|null
     */
    private $updated_at;


    /**
     * @ORM\Column(type="string", length=256)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="ad", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CommandeArticle", mappedBy="Product")
     */
    private $commandeArticles;

    /**
     * @ORM\Column(type="boolean", options={"default":"1"})
     */
    private $activer=true;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $sex;

    /**
     * @ORM\OneToMany(targetEntity=Images::class, mappedBy="produits",cascade={"persist"}, orphanRemoval=true
    )
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity=Marque::class, inversedBy="products")
     */
    private $marque;

    /**
     * @ORM\ManyToOne  (targetEntity=Category::class, inversedBy="products")
     */
    private $categories;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Subcategory", inversedBy="product")
     */
    private $subCategory;

    /**
     * @ORM\ManyToOne (targetEntity=SubSubCategories::class, inversedBy="products")
     */
    private $subSubCategories ;

    /**
     * @ORM\Column(type="string", length=256,nullable=true)
     */
    private $refrence;

    /**
     * @ORM\ManyToOne(targetEntity=Style::class, inversedBy="products")
     */
    private $style;

    /**
     * @ORM\ManyToOne(targetEntity=Forme::class, inversedBy="products")
     */
    private $forme;

    /**
     * @ORM\ManyToOne(targetEntity=Cadre::class, inversedBy="products")
     */
    private $cadres;

    /**
     * @ORM\ManyToOne(targetEntity=MatierBracelet::class, inversedBy="products")
     */
    private $matierBracelet;

    /**
     * @ORM\ManyToOne(targetEntity=TypeDuMouvement::class, inversedBy="products")
     */
    private $typeDuMouvement;

    /**
     * @ORM\ManyToOne(targetEntity=FonctionMontre::class, inversedBy="products")
     */
    private $fonctionMontre;

    /**
     * @ORM\ManyToOne(targetEntity=FormeDuCadran::class, inversedBy="products")
     */
    private $formeDuCadran;

    /**
     * @ORM\ManyToOne(targetEntity=VerreDeMontre::class, inversedBy="products")
     */
    private $verreDeMontre;

    /**
     * @ORM\ManyToOne(targetEntity=PlaquettesDeNez::class, inversedBy="products")
     */
    private $plaquettesDeNez;

    /**
     * @ORM\ManyToOne(targetEntity=MatieresDuLunette::class, inversedBy="products")
     */
    private $matieresDuLunette;

    /**
     * @ORM\ManyToOne(targetEntity=MatiereDuBranche::class, inversedBy="products")
     */
    private $matiereDuBranche;

    /**
     * @ORM\ManyToOne(targetEntity=Volume::class, inversedBy="products")
     */
    private $volume;

    /**
     * @ORM\ManyToOne(targetEntity=TypeDeMaquillage::class, inversedBy="products")
     */
    private $typeDeMaquillage;

    /**
     * @ORM\ManyToOne(targetEntity=FragranceDeParfum::class, inversedBy="products")
     */
    private $fragranceDeParfum;


    /**
     * @var string
     *
     * @ORM\Column(name="link_youtube", type="string", length=256, nullable=true)
     */
    private $linkYoutube;


    /**
     * @ORM\Column(type="text")
     */
    private $easeOfPayment;

    public function __construct()
    {
        $this->commandeArticles = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->created_at = new DateTime();
        return $this->created_at->modify("+ 1 hour");
    }

    /**
     * Get Note Globale Annonce
     * @return int
     */
    public function getAvgRatings()
    {
        $sum = array_reduce($this->comments->toArray(), function ($total, $comment) {
            return  ceil($total + $comment->getRating());
        }, 0);

        if (count($this->comments) > 0) {
            return ceil($sum / count($this->comments));
        } else {
            return 0;
        }
    }


    /**
     * Get Commentaire + Notation User sur annonce
     * @param User $author
     * @return Comment|null
     */
    public function getCommentFromAuthor(User $author)
    {
        foreach ($this->comments as $comment) {
            if ($comment->getAuthor() === $author) {
                return $comment;
            }
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Product
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getPromo()
    {
        return $this->promo;
    }

    /**
     * @param mixed $promo
     * @return Product
     */
    public function setPromo($promo)
    {
        $this->promo = $promo;
        return $this;
    }

    /**
     * @return int
     */
    public function getPricePromo(): int
    {
        return $this->pricePromo;
    }

    /**
     * @param int $pricePromo
     * @return Product
     */
    public function setPricePromo(int $pricePromo): Product
    {
        $this->pricePromo = $pricePromo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNewprice()
    {
        return $this->newprice;
    }

    /**
     * @param mixed $newprice
     * @return Product
     */
    public function setNewprice($newprice)
    {
        $this->newprice = $newprice;
        return $this;
    }



    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string|null $filename
     * @return Product
     */
    public function setFilename(?string $filename): Product
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param File|null $imageFile
     * @return Product
     */
    public function setImageFile(?File $imageFile): Product
    {
        $this->imageFile = $imageFile;
        if ($this->imageFile instanceof UploadedFile) {
            $this->updated_at = new DateTime('now');
        }
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    /**
     * @param DateTime $created_at
     * @return Product
     */
    public function setCreatedAt(DateTime $created_at): Product
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updated_at;
    }

    /**
     * @param DateTime|null $updated_at
     * @return Product
     */
    public function setUpdatedAt(?DateTime $updated_at): Product
    {
        $this->updated_at = $updated_at;
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
     * @return Product
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
            $commandeArticle->setProduct($this);
        }

        return $this;
    }

    public function removeCommandeArticle(CommandeArticle $commandeArticle): self
    {
        if ($this->commandeArticles->contains($commandeArticle)) {
            $this->commandeArticles->removeElement($commandeArticle);
            // set the owning side to null (unless already changed)
            if ($commandeArticle->getProduct() === $this) {
                $commandeArticle->setProduct(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAd($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAd() === $this) {
                $comment->setAd(null);
            }
        }

        return $this;
    }



    /*
     * @return Collection|Images[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProduits($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->removeElement($image)) {
            if ($image->getProduits() === $this) {
                $image->setProduits(null);
            }
        }

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
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @param mixed $sex
     */
    public function setSex($sex): void
    {
        $this->sex = $sex;
    }

    public function getMarque(): ?Marque
    {
        return $this->marque;
    }

    public function setMarque(?Marque $marque): self
    {
        $this->marque = $marque;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRefrence()
    {
        return $this->refrence;
    }

    /**
     * @param mixed $refrence
     */
    public function setRefrence($refrence): void
    {
        $this->refrence = $refrence;
    }


    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    public function setCategories(?Category $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubCategory()
    {
        return $this->subCategory;
    }

    public function setSubCategory(?Subcategory $subcategory): self
    {
        $this->subCategory = $subcategory;
        return $this;
    }

    /**
     * @return Style|null
     */
    public function getStyle(): ?Style
    {
        return $this->style;
    }

    /**
     * @param mixed $style
     */
    public function setStyle(?Style $style): Product
    {
        $this->style = $style;
        return $this;
    }

    /**
     * @return Forme|null
     */
    public function getForme(): ?Forme
    {
        return $this->forme;
    }

    /**
     * @param Forme|null $forme
     * @return Product
     */
    public function setForme(?Forme $forme): Product
    {
        $this->forme = $forme;
        return $this;
    }

    /**
     * @return Cadre|null
     */
    public function getCadres(): ?Cadre
    {
        return $this->cadres;
    }

    /**
     * @param Cadre|null $cadres
     * @return $this
     */
    public function setCadres(?Cadre $cadres): Product
    {
        $this->cadres = $cadres;
        return $this;
    }

    /**
     * @return SubSubCategories|null
     */
    public function getSubSubCategories(): ?SubSubCategories
    {
        return $this->subSubCategories;
    }

    /**
     * @param SubSubCategories $subSubCategories
     * @return $this|null
     */
    public function setSubSubCategories(SubSubCategories  $subSubCategories): ?Product
    {
        $this->subSubCategories = $subSubCategories;
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
    public function setMatierBracelet(?MatierBracelet $matierBracelet): Product
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
     * @return $this
     */
    public function setTypeDuMouvement(?TypeDuMouvement $typeDuMouvement): Product
    {
        $this->typeDuMouvement = $typeDuMouvement;
        return  $this;
    }

    /**
     * @return FonctionMontre|null
     */
    public function getFonctionMontre(): ?FonctionMontre
    {
        return $this->fonctionMontre;
    }

    /**
     * @param FonctionMontre|null $fonctionMontre
     * @return $this
     */
    public function setFonctionMontre(?FonctionMontre $fonctionMontre): Product
    {
        $this->fonctionMontre = $fonctionMontre;
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
    public function setProducts(?Product  $products): Forme
    {
        $this->products = $products;
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
    public function setFormeDuCadran(?FormeDuCadran  $formeDuCadran): Product
    {
        $this->formeDuCadran = $formeDuCadran;
        return  $this;
    }

    /**
     * @return VerreDeMontre|null
     */
    public function getVerreDeMontre(): ?VerreDeMontre
    {
        return $this->verreDeMontre;
    }

    /**
     * @param mixed $verreDeMontre
     */
    public function setVerreDeMontre(?VerreDeMontre $verreDeMontre): Product
    {
        $this->verreDeMontre = $verreDeMontre;
        return $this;
    }

    /**
     * @return PlaquettesDeNez|null
     */
    public function getPlaquettesDeNez(): ?PlaquettesDeNez
    {
        return $this->plaquettesDeNez;
    }

    /**
     * @param PlaquettesDeNez|null $plaquettesDeNez
     * @return $this
     */
    public function setPlaquettesDeNez(?PlaquettesDeNez $plaquettesDeNez): Product
    {
        $this->plaquettesDeNez = $plaquettesDeNez;
        return $this;
    }

    /**
     * @return MatieresDuLunette|null
     */
    public function getMatieresDuLunette(): ?MatieresDuLunette
    {
        return $this->matieresDuLunette;
    }

    /**
     * @param MatieresDuLunette|null $matieresDuLunette
     * @return $this
     */
    public function setMatieresDuLunette(?MatieresDuLunette $matieresDuLunette): Product
    {
        $this->matieresDuLunette = $matieresDuLunette;
        return $this;
    }

    /**
     * @return MatiereDuBranche|null
     */
    public function getMatiereDuBranche(): ?MatiereDuBranche
    {
        return $this->matiereDuBranche;
    }

    /**
     * @param MatiereDuBranche|null $matiereDuBranche
     * @return $this
     */
    public function setMatiereDuBranche(?MatiereDuBranche $matiereDuBranche): Product
    {
        $this->matiereDuBranche = $matiereDuBranche;

        return $this;
    }

    /**
     * @return Volume|null
     */
    public function getVolume(): ?Volume
    {
        return $this->volume;
    }

    /**
     * @param Volume|null $volume
     * @return $this
     */
    public function setVolume(?Volume $volume): Product
    {
        $this->volume = $volume;

        return $this;
    }

    /**
     * @return TypeDeMaquillage|null
     */
    public function getTypeDeMaquillage(): ?TypeDeMaquillage
    {
        return $this->typeDeMaquillage;
    }

    /**
     * @param TypeDeMaquillage|null $typeDeMaquillage
     * @return $this
     */
    public function setTypeDeMaquillage(?TypeDeMaquillage $typeDeMaquillage): Product
    {
        $this->typeDeMaquillage = $typeDeMaquillage;

        return $this;
    }

    public function getFragranceDeParfum(): ?FragranceDeParfum
    {
        return $this->fragranceDeParfum;
    }

    public function setFragranceDeParfum(?FragranceDeParfum $fragranceDeParfum): self
    {
        $this->fragranceDeParfum = $fragranceDeParfum;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLinkYoutube(): ?string
    {
        return $this->linkYoutube;
    }

    /**
     * @param string|null $linkYoutube
     * @return Product
     */
    public function setLinkYoutube(?string $linkYoutube): Product
    {
        $this->linkYoutube = $linkYoutube;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEaseOfPayment()
    {
        return $this->easeOfPayment;
    }

    /**
     * @param mixed $easeOfPayment
     */
    public function setEaseOfPayment($easeOfPayment): void
    {
        $this->easeOfPayment = $easeOfPayment;
    }
}
