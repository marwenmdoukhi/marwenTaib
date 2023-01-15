<?php

namespace App\Manager;

use App\Entity\Commande;
use App\Entity\CommandeArticle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CommandeManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var \Symfony\Component\Security\Core\User\UserInterface|null
     */
    private $user;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->user = $security->getUser();
    }

    public function commander($data): array
    {
        $cmdArticle = new CommandeArticle();
        $cmdArticle->setQuantite($data['cart']);
        $cmdArticle->setProduct($data['produit']);
        $cmdArticle->setCommande($data['commande']);
        $this->entityManager->persist($cmdArticle);
        $this->entityManager->flush();
        return $data;
    }
}
