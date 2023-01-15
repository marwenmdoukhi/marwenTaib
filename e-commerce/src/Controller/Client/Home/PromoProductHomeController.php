<?php

namespace App\Controller\Client\Home;

use App\Repository\ProductRepository;
use App\Utils\Interfaces\CacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PromoProductHomeController extends AbstractController
{
    /**
     * @Route("/PromoProductHome", name="PromoProductHome")
     */
    public function __invoke(ProductRepository  $productRepository, CacheInterface $cache)
    {
        $cache = $cache->cache;
        $cachedItem = $cache->getItem("PromoProductHome");
        $cache->save($cachedItem);
        $cachedItem->expiresAfter(3600);
        if (!$cachedItem->isHit()) {
            $response = $this->render('client/home/promoproducthome.html.twig', [
                'promo'=>$productRepository->pormoProduitClientHome(),
                'promomobils'=> $productRepository->pormoProduitClientHomeMobile(),
            ]);
            $cachedItem->set($response);
            $cache->save($cachedItem);
        }
        return $cachedItem->get();

    }
}
