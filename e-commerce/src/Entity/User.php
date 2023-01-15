<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="Cette adresse e-mail est déjà utilisée")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=256)
     *   @Assert\NotBlank()
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=256)
     * @Assert\NotBlank()
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=256, unique=true)
     * @Assert\Email(message="Veuillez renseigner une adresse e-mail valide")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=256,nullable=true)
     */
    private $picture;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $hash;


    /**
     * @Assert\EqualTo(propertyPath="hash",message="vous n'est pas correctement confirmer" )
     */
    public $passwordConfirmer;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $adress;

    /**
     *  @Assert\Length(min = 8, max = 20, minMessage = "Veuillez insérer un numéro de téléphone valide de 8 chiffres.", maxMessage = "Veuillez insérer un numéro de téléphone valide de 8 chiffres.")
    *   @Assert\Regex(pattern="/^[0-9]*$/", message="number_only")
     * @ORM\Column(type="string", length=256)
     */
    private $tel;

    /**
     * @ORM\ManyToMany(targetEntity=Roles::class, mappedBy="users",cascade={"persist"})
     */
    private $usersRoles;


    /**
     *@ORM\OneToMany(targetEntity="App\Entity\Commande", mappedBy="users")
     */
    public $Commande;


    /**
     * @ORM\OneToMany (targetEntity="App\Entity\Comment", mappedBy="author")
     * @ORM\JoinColumn(nullable=false)
     */
    private $comments;


    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $reset_token;

    /**
     * @ORM\Column(type="integer", length=10)
     */
    private $codepostal;

    public function __construct()
    {
        $this->usersRoles = new ArrayCollection();
        $this->Commande = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function fullname()
    {
        return"$this->firstName $this->lastName ";
    }


    public function getPassword()
    {
        return $this->hash;
        // TODO: Implement getPassword() method.
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        return $this->email;
        // TODO: Implement getUsername() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }


    public function getRoles()
    {
        $roles=$this->usersRoles->map(function ($role) {
            return $role->getTitle();
        })->toArray();
        $roles[] = "ROLE_USER";
        return $roles;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
     * @return User
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     * @return User
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAdress()
    {
        return $this->adress;
    }

    /**
     * @param mixed $adress
     * @return User
     */
    public function setAdress($adress)
    {
        $this->adress = $adress;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * @param mixed $tel
     * @return User
     */
    public function setTel($tel)
    {
        $this->tel = $tel;
        return $this;
    }

    /**
     * @return Collection|Roles[]
     */
    public function getUsersRoles(): Collection
    {
        return $this->usersRoles;
    }

    public function addUsersRole(Roles $usersRole): self
    {
        if (!$this->usersRoles->contains($usersRole)) {
            $this->usersRoles[] = $usersRole;
            $usersRole->addUser($this);
        }

        return $this;
    }

    public function removeUsersRole(Roles $usersRole): self
    {
        if ($this->usersRoles->contains($usersRole)) {
            $this->usersRoles->removeElement($usersRole);
            $usersRole->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|Commande[]
     */
    public function getCommande(): Collection
    {
        return $this->Commande;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->Commande->contains($commande)) {
            $this->Commande[] = $commande;
            $commande->setUsers($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->Commande->contains($commande)) {
            $this->Commande->removeElement($commande);
            // set the owning side to null (unless already changed)
            if ($commande->getUsers() === $this) {
                $commande->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param mixed $comments
     * @return User
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->reset_token;
    }

    public function setResetToken(?string $reset_token): self
    {
        $this->reset_token = $reset_token;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodepostal()
    {
        return $this->codepostal;
    }

    /**
     * @param mixed $codepostal
     * @return User
     */
    public function setCodepostal($codepostal)
    {
        $this->codepostal = $codepostal;
        return $this;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }
}
