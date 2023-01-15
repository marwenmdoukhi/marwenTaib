<?php

namespace App\Controller\Client\Home;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Utils\Interfaces\CacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesHomeController extends AbstractController
{
    /**
     * @Route("/categoriesHome", name="categoriesHome")
     */
    public function __invoke(CategoryRepository  $categoryRepository, ProductRepository  $productRepository, CacheInterface $cache)
    {
        $cache = $cache->cache;
        $cachedItem = $cache->getItem("categoriesHome");
        $cache->save($cachedItem);
        $cachedItem->expiresAfter(3600);
        if (!$cachedItem->isHit()) {
            $response = $this->render('client/home/categories.html.twig', [
                'category'=>$categoryRepository->findBy(['activer'=>1]),
                'promo'=> $productRepository->totalpromo(),
            ]);
            $cachedItem->set($response);
            $cache->save($cachedItem);
        }
        return $cachedItem->get();
    }
}
