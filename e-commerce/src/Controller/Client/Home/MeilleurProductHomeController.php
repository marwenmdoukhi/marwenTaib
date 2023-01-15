<?php

namespace App\Controller\Client\Home;

use App\Repository\ProductRepository;
use App\Utils\Interfaces\CacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MeilleurProductHomeController extends AbstractController
{
    /**
     * @Route("/meilleurProductHome", name="meilleurProductHome")
     */
    public function __invoke(ProductRepository  $productRepository, CacheInterface $cache)
    {
        $cache = $cache->cache;
        $cachedItem = $cache->getItem("MProductHome");
        $cache->save($cachedItem);
        $cachedItem->expiresAfter(3600);
        if (!$cachedItem->isHit()) {
            $response = $this->render('client/home/meilleurproducthome.html.twig', [
                'meilleurproduit'=>$productRepository->meilleurproduitHome(),
                'meilleurproduitMobile'=>$productRepository->meilleurproduitHomeMobile(),

            ]);
            $cachedItem->set($response);
            $cache->save($cachedItem);
        }
        return $cachedItem->get();
    }
}
