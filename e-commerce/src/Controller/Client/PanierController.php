<?php

namespace App\Controller\Client;

use App\Service\Cart\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/panier", name="cart_index")
     * @param CartService $cartService
     * @return RedirectResponse|Response
     */
    public function index(CartService $cartService)
    {
        if (empty($cartService->getfullCarte())) {
            return  $this->redirectToRoute('home');
        }
        $tab=  $cartService->getfullCarte();
        $total=$cartService->gettotal();
        return $this->render('client/panier/index.html.twig', [
            'items'=>$tab,
            'total'=>$total,
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add")
     * @param CartService $cart
     * @param $id
     * @return Response
     */
    public function add(CartService $cart, $id): Response
    {
        $cart->add($id);
        return $this->redirectToRoute('cart_index');
    }

    /**
     * @Route("/panier/add/{id}", name="cart_add_multiple")
     * @param $id
     * @param SessionInterface $session
     * @param Request $request
     * @return RedirectResponse
     */
    public function addmultiple($id, CartService $cart)
    {
        $cart->addmuliple($id);
        return $this->redirectToRoute('cart_index', [
        ]);
    }

    /**
     * @Route("/cart/decrease/{id}", name="decrease_to_cart")
     * @param CartService $cart
     * @param $id
     * @return Response
     */
    public function decrease(CartService $cart, $id): Response
    {
        $cart->decrease($id);
        return $this->redirectToRoute('cart_index');
    }

    /**
     * @Route("/panier/remove/{id}", name="carte_remove")
     * @param $id
     * @param CartService $cartService
     * @return RedirectResponse
     */
    public function remove($id, CartService $cartService)
    {
        $cartService->remove($id);
        return $this->redirectToRoute('cart_index');
    }
}
