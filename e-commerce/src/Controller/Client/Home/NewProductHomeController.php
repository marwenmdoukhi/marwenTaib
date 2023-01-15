<?php

namespace App\Controller\Client\Home;

use App\Repository\ProductRepository;
use App\Utils\Interfaces\CacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class NewProductHomeController extends AbstractController
{
    /**
     * @Route("/NewProductHome", name="NewProductHome")
     */
    public function __invoke(ProductRepository  $productRepository, CacheInterface $cache)
    {
        $cache = $cache->cache;
        $cachedItem = $cache->getItem("NewProductHome");
        $cache->save($cachedItem);
        $cachedItem->expiresAfter(3600);
        if (!$cachedItem->isHit()) {
            $response = $this->render('client/home/newproducthome.html.twig', [
                'nouveautes'=>$productRepository->nouveauxProduitClientHome(),
                'nouveautesMobile'=>$productRepository->nouveauxProduitClientHomeMobile(),

            ]);
            $cachedItem->set($response);
            $cache->save($cachedItem);
        }
        return $cachedItem->get();
    }
}
