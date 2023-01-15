<?php

namespace App\Controller\Admin;

use App\Entity\Images;
use App\Entity\Product;
use App\Form\Configuration\ProductConfigurationHorlogrieType;
use App\Form\Configuration\ProductConfigurationOptiqueType;
use App\Form\Configuration\ProductConfigurationParfumeType;
use App\Form\ProductType;
use App\Helper\FormatUrlYoutube;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/configuration")
 */
class AdminConfigController extends AbstractController
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/configration_optique/{id}", name="configration_optique")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $manager, $id)
    {
        $produit= $this->productRepository->find($id);
        $form = $this->createForm(ProductConfigurationOptiqueType::class, $produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($produit);
            $manager->flush();
            return $this->redirectToRoute('product_index');
        }
        return $this->render('admin/product/Configuration/optique.html.twig', [
            'form' => $form->createView(),
            'product'=>$produit->getName()
        ]);
    }

    /**
     * @Route("/configration_Horlogrie/{id}", name="configration_horlogrie")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function horlogrie(Request $request, EntityManagerInterface $manager, $id)
    {
        $produit= $this->productRepository->find($id);
        $form = $this->createForm(ProductConfigurationHorlogrieType::class, $produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($produit);
            $manager->flush();
            return $this->redirectToRoute('product_index');
        }
        return $this->render('admin/product/Configuration/horlogrie.html.twig', [
            'form' => $form->createView(),
            'product'=>$produit->getName()
        ]);
    }


    /**
     * @Route("/configration_parfume/{id}", name="configration_parfume")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function parfume(Request $request, EntityManagerInterface $manager, $id)
    {
        $produit= $this->productRepository->find($id);
        $form = $this->createForm(ProductConfigurationParfumeType::class, $produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($produit);
            $manager->flush();
            return $this->redirectToRoute('product_index');
        }
        return $this->render('admin/product/Configuration/parfume.html.twig', [
            'form' => $form->createView(),
            'product'=>$produit->getName()
        ]);
    }

    /**
     * @Route("/configration_occasion/{id}", name="configration_occasion")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function occasion(Request $request, EntityManagerInterface $manager, $id)
    {
        $produit= $this->productRepository->find($id);
        $form = $this->createForm(ProductConfigurationParfumeType::class, $produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($produit);
            $manager->flush();
            return $this->redirectToRoute('product_index');
        }
        return $this->render('admin/product/Configuration/parfume.html.twig', [
            'form' => $form->createView(),
            'product'=>$produit->getName()
        ]);
    }
}
