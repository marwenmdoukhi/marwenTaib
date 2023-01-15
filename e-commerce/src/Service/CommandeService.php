<?php

namespace App\Service;

use App\Entity\Commande;
use App\Entity\CommandeArticle;
use App\Event\ContactEvent;
use App\Repository\CommandeArticleRepository;
use App\Repository\CommandeRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\RouterInterface;

class CommandeService
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
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var SwiftMailerService
     */
    private $swiftMailerService;

    /**
     * @var \Twig\Environment
     */
    private $templating;


    /**
     * @param CommandeRepository $commandeRepository
     * @param CommandeArticleRepository $commandeArticleRepository
     * @param EntityManagerInterface $entityManager
     * @param ProductRepository $productRepository
     * @param Security $security
     * @param RouterInterface $router
     * @param RequestStack $request
     * @param SwiftMailerService $swiftMailerService
     */
    public function __construct(
        CommandeRepository $commandeRepository,
        CommandeArticleRepository $commandeArticleRepository,
        EntityManagerInterface $entityManager,
        ProductRepository  $productRepository,
        Security $security,
        RouterInterface $router,
        RequestStack   $request,
        SwiftMailerService  $swiftMailerService,
        \Twig\Environment $templating
    ) {
        $this->commandeRepository = $commandeRepository;
        $this->commandeArticleRepository = $commandeArticleRepository;
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->user = $security->getUser();
        $this->router = $router;
        $this->request = $request;
        $this->swiftMailerService = $swiftMailerService;
        $this->templating = $templating;
    }

    public function Commande()
    {
        $session =  $this->request->getCurrentRequest()->getSession();
        if (!$this->user) {
            return new RedirectResponse($this->router->generate('account_login'));
        }
        if (!$session->has('panier')) {
            return new RedirectResponse($this->router->generate('home'));
        }
        $commande = new Commande();
        $cmdArticle = new CommandeArticle();
        $cart = $session->get('panier');
        $produits =  $this->productRepository->getArray(array_keys($cart));
        $cartCmd = [];
        $totalNet = 0;
        foreach ($produits as $produit) {
            $totalU = ($produit->getNewprice() * $cart[$produit->getId()]);
            $totalNet += floatval($totalU);
            $cartCmd[$produit->getId()] = [
                'prixU' => $produit->getNewprice(),
                'quantite' => intval($cart[$produit->getId()]),
                'totalU' => $totalU,];
            $cmdArticle->setQuantite($cart[$produit->getId()]);
            $cmdArticle->setProduct($produit);
            $cmdArticle->setCommande($commande);
            $this->entityManager->persist($cmdArticle);
        }
        $commande->setUsers($this->user);
        $commande->setCartCmd($cartCmd);
        $commande->setMontant($totalNet);
        $commande->setPayer(0);
        $this->entityManager->persist($commande);
        $this->entityManager->flush();
        $session->remove('panier');
        $mescommande =$this->commandeRepository->orderMail($this->user, $commande->getNumero());
        $detailcommande=$this->commandeArticleRepository->detailOrderMail($this->user, $commande->getNumero());

//        $message = (new \Swift_Message('commande  '))
//            ->setFrom('wf dali')
//            ->setTo($userconncter)
//            ->setCc("dali");
//
//        $message->setBody(
//            $this-> $this->templating->render(
//                'email/order.html.twig', array('mescommande'=>$mescommande,'detailcommande'=>$detailcommande)
//            ), 'text/html'
//        );
//        try {
//            $this->get('mailer')->send($message);
//        } catch (FileException $e) {
//            return new Response(0);
//        }
        return new RedirectResponse($this->router->generate('checkout', ['id' => $commande->getNumero()]));
    }
}
