<?php

namespace App\Controller\Client;

use App\Repository\CategoryRepository;
use App\Repository\NewsRepository;
use App\Service\Cart\CartService;
use App\Utils\Interfaces\CacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FooterController extends AbstractController
{
    /**
     * @Route("/footer", name="footer")
     */
    public function __invoke(CacheInterface $cache)
    {
        $cache = $cache->cache;
        $cachedItem = $cache->getItem("footer");
        $cache->save($cachedItem);

        if (!$cachedItem->isHit()) {
            $response = $this->render('client/layout/footer.html.twig');
            $cachedItem->set($response);
            $cache->save($cachedItem);
        }
        return $cachedItem->get();
    }
}
