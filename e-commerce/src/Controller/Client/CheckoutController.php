<?php

namespace App\Controller\Client;

use App\Manager\CommandeManager;
use App\Repository\CommandeArticleRepository;
use App\Repository\CommandeRepository;
use App\Service\CommandeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    /**
     * @var CommandeRepository
     */
    protected $commandeRepository;

    /**
     * @var CommandeArticleRepository
     */
    private $commandeArticleRepository;

    /**
     * @param CommandeRepository $commandeRepository
     * @param CommandeArticleRepository $commandeArticleRepository
     * @param EntityManagerInterface $entityManager
     * @param CommandeManager $manager
     */
    public function __construct(
        CommandeRepository $commandeRepository,
        CommandeArticleRepository $commandeArticleRepository,
        EntityManagerInterface $entityManager,
        CommandeManager $manager
    ) {
        $this->commandeRepository = $commandeRepository;
        $this->commandeArticleRepository = $commandeArticleRepository;
        $this->entityManager = $entityManager;
        $this->manager = $manager;
    }

    /**
     * @Route("/commande", name="commande")
     * @param CommandeService $service
     * @return RedirectResponse
     */
    public function validationCommandeAction(CommandeService  $service)
    {
        return $service->Commande();
    }

    /**
     * @Route("/checkout-facture={id}", name="checkout")
     */
    public function index($id): Response
    {
        if (! $this->getUser()) {
            return $this->redirectToRoute('home');
        }
        $mescommande =$this->commandeRepository->order($this->getUser(), $id);
        $detailcommande=$this->commandeArticleRepository->detailOrderMail($this->getUser(), $id);
        return $this->render('client/checkout/index.html.twig', [
            'order'=>$mescommande,
            'detailcommande' => $detailcommande,
        ]);
    }
}
