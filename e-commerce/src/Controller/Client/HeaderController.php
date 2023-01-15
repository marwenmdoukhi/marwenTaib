<?php

namespace App\Controller\Client;

use App\Repository\CategoryRepository;
use App\Repository\NewsRepository;
use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HeaderController extends AbstractController
{
    /**
     * @var NewsRepository
     */
    private $newsRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var CartService
     */
    private $cartService;

    public function __construct(NewsRepository $newsRepository, CategoryRepository$categoryRepository, CartService $cartService)
    {
        $this->newsRepository = $newsRepository;
        $this->categoryRepository = $categoryRepository;
        $this->cartService = $cartService;
    }

    /**
     * @Route("/header", name="header")
     */
    public function __invoke()
    {
        return $this->render('client/layout/header.html.twig', [
            'news'=>$this->newsRepository->findAll(),
            'category'=>$this->categoryRepository->findBy(['activer'=>1]),
            'items'=> $this->cartService->getfullCarte(),
            'total'=>$this->cartService->gettotal()
        ]);
    }
}
