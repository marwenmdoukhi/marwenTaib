<?php

namespace App\Controller\Client\BoutiqueSolde;

use App\Data\Boutique\SearchSolde;
use App\Form\Boutique\SearchPromoForm;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SoldeAction extends AbstractController
{
    /**
     * @Route("/boutique-promo", name="boutiquesolde")
     */
    public function __invoke(ProductRepository  $productRepository, Request $request)
    {
        $data = new SearchSolde();
        $data->page = $request->get('page', 1);
        $form=$this->createForm(SearchPromoForm::class, $data);
        [$min, $max] = $productRepository->findMinMaxSolde($data);
        $form->handleRequest($request);
        $product=$productRepository->findSearchSolde($data);

        return $this->render('client/boutique/solde/index.html.twig', [
            'boutiquesolde'=> $product,
            'form'=>$form->createView(),
            'min'=>$min,
            'max'=>$max,
        ]);
    }

//    public function index(ProductRepository  $productRepository,Request $request, CacheInterface $cache)
//    {
//        $cache = $cache->cache;
//        $cachedItem = $cache->getItem("boutiquesolde");
//        $cachedItem->expiresAfter(1800);
//        if (!$cachedItem->isHit()){
//            $data = new SearchSolde();
//            $data->page = $request->get('page', 1);
//            $form = $this->createForm(SearchPromoForm::class);
//            [$min, $max] = $productRepository->findMinMaxSolde($data);
//            $form->handleRequest($request);
//            $products = $productRepository->findSearchSolde($data);
//            $cachedItem->set($products);
//            $cache->save($cachedItem);
//            $response = $this->render('client/boutique/solde/index.html.twig', [
//                'products' => $products,
//                'form' => $form->createView(),
//                'min' => $min,
//                'max' => $max,
//            ]);
//            $cachedItem->set($response);
//            $cache->save($cachedItem);
//        }
//        return $cachedItem->get();
//    }
}
