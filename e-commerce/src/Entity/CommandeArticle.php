<?php

namespace App\Entity;

use App\Repository\CommandeArticleRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=CommandeArticleRepository::class)
 * @Vich\Uploadable
 */
class CommandeArticle
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $Quantite;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Commande", inversedBy="commandeArticles",cascade={"persist"})
     */
    private $Commande;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="commandeArticles",cascade={"persist"})
     */
    private $Product;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return CommandeArticle
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuantite()
    {
        return $this->Quantite;
    }

    /**
     * @param mixed $Quantite
     * @return CommandeArticle
     */
    public function setQuantite($Quantite)
    {
        $this->Quantite = $Quantite;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCommande()
    {
        return $this->Commande;
    }

    /**
     * @param mixed $Commande
     * @return CommandeArticle
     */
    public function setCommande($Commande)
    {
        $this->Commande = $Commande;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->Product;
    }

    /**
     * @param mixed $Product
     * @return CommandeArticle
     */
    public function setProduct($Product)
    {
        $this->Product = $Product;
        return $this;
    }
}
