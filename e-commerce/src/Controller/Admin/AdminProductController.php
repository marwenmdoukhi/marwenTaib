<?php

namespace App\Controller\Admin;

use App\Entity\Images;
use App\Entity\Product;
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
 * @Route("/admin/produit")
 */
class AdminProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index", methods={"GET"})
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('admin/product/index.html.twig', [
            'products' => $productRepository->produit(),
            'current_link' => 'produit',
        ]);
    }

    /**
     * @Route("/new", name="product_new")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $manager, FormatUrlYoutube $formatUrlYoutube)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            $youtube=$form->get('linkYoutube')->getData();
            foreach ($images as $image) {
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $img = new Images();
                $img->setName($fichier);
                $product->addImage($img);
            }
            $product->setNewprice($product->getPrice() - ($product->getPrice() * $product->getPricePromo() / 100));
            $linkyoutube=$formatUrlYoutube::format($youtube);
            if (true === isset($youtube)) {
                $product->setLinkYoutube($linkyoutube);
            }
            $manager->persist($product);
            $manager->flush();
            if ($product->getCategories()=="Optique") {
                return $this->redirectToRoute('configration_optique', ['id'=>$product->getId()]);
            }
            if ($product->getCategories()=="Horlogerie") {
                return $this->redirectToRoute('configration_horlogrie', ['id'=>$product->getId()]);
            }
            if ($product->getCategories()=="Parfumerie") {
                return $this->redirectToRoute('configration_parfume', ['id'=>$product->getId()]);
            }
            if ($product->getCategories()=="occasion") {
                return $this->redirectToRoute('product_index');
            }
            if ($product->getCategories()=="Occasion") {
                if (($product->getSubCategory()->getName() =="Lunettes De soleil") ||($product->getSubCategory()->getName() =="Cadres Optiques")) {
                    return $this->redirectToRoute('configration_optique', ['id'=>$product->getId()]);
                }
                if (($product->getSubCategory()->getName() =="Montres Pour Hommes") ||($product->getSubCategory()->getName() =="Montres Pour Femmes")
                    ||($product->getSubCategory()->getName() =="Montres Pour Enfants") ||($product->getSubCategory()->getName() =="Smartwatches")) {
                    return $this->redirectToRoute('configration_horlogrie', ['id'=>$product->getId()]);
                }
                if (($product->getSubCategory()->getName() =="Parfums") ||($product->getSubCategory()->getName() =="Maquillage")) {
                    return $this->redirectToRoute('configration_parfume', ['id'=>$product->getId()]);
                } else {
                    return $this->redirectToRoute('product_index');
                }
            }
        }
        return $this->render('admin/product/new.html.twig', [
            'Boutique' => $product,
            'form' => $form->createView(),
            'current_link' => 'produit',

        ]);
    }

    /**
     * @Route("/{slug}-{id}", name="product_show",requirements={"slug": "[a-z0-9\-]*"})
     * @param Product $product
     * @param string $slug
     * @return Response
     */
    public function show(Product $product, string $slug): Response
    {
        if ($product->getSlug() !== $slug) {
            return $this->redirectToRoute('product_show', [
                'id' => $product->getId(),
                'slug' => $product->getSlug(),
                'current_link' => 'produit',
            ], 301);
        }
        return $this->render('admin/product/show.html.twig', [
            'product' => $product,
            'current_link' => 'produit',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", requirements={"slug": "[a-z0-9\-]*"})
     * @param Request $request
     * @param Product $product
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Request $request, Product $product, EntityManagerInterface $manager, FormatUrlYoutube  $formatUrlYoutube): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            $promo = $form->get('promo')->getData();
            $youtube=$form->get('linkYoutube')->getData();
            foreach ($images as $image) {
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $img = new Images();
                $img->setName($fichier);
                $product->addImage($img);
            }
            $linkyoutube=$formatUrlYoutube::format($youtube);
            if (true === isset($youtube)) {
                $product->setLinkYoutube($linkyoutube);
            }
            if ($promo === true) {
                $product->setNewprice($product->getPrice() - ($product->getPrice() * $product->getPricePromo() / 100));
            } else {
                $product->setNewprice($product->getPrice());
            }
            $manager->persist($product);
            $manager->flush();
            if ($product->getCategories()=="Optique") {
                return $this->redirectToRoute('configration_optique', ['id'=>$product->getId()]);
            }
            if ($product->getCategories()=="Horlogerie") {
                return $this->redirectToRoute('configration_horlogrie', ['id'=>$product->getId()]);
            }
            if ($product->getCategories()=="Parfumerie") {
                return $this->redirectToRoute('configration_parfume', ['id'=>$product->getId()]);
            }
            if ($product->getCategories()=="occasion") {
                return $this->redirectToRoute('product_index');
            }
            if ($product->getCategories()=="Occasion") {
                if (($product->getSubCategory()->getName() =="Lunettes De soleil") ||($product->getSubCategory()->getName() =="Cadres Optiques")) {
                    return $this->redirectToRoute('configration_optique', ['id'=>$product->getId()]);
                }
                if (($product->getSubCategory()->getName() =="Montres Pour Hommes") ||($product->getSubCategory()->getName() =="Montres Pour Femmes")
                    ||($product->getSubCategory()->getName() =="Montres Pour Enfants") ||($product->getSubCategory()->getName() =="Smartwatches")) {
                    return $this->redirectToRoute('configration_horlogrie', ['id'=>$product->getId()]);
                }
                if (($product->getSubCategory()->getName() =="Parfums") ||($product->getSubCategory()->getName() =="Maquillage")) {
                    return $this->redirectToRoute('configration_parfume', ['id'=>$product->getId()]);
                } else {
                    return $this->redirectToRoute('product_index');
                }
            }
            return $this->redirectToRoute('product_index');
        }
        return $this->render('admin/product/edit.html.twig', [
            'Boutique' => $product,
            'form' => $form->createView(),
            'current_link' => 'produit',

        ]);
    }

    /**
     * @Route("/delete/{id}", name="product_delete")
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function delete(Request $request, Product $product): Response
    {
        $images =$product->getImages();
        if ($images) {
            foreach ($images as $image) {
                $nomImage = $this->getParameter("images_directory") . '/' . $image->getName();
                if (file_exists($nomImage)) {
                    unlink($nomImage);
                }
            }
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($product);
        $entityManager->flush();
        return $this->redirectToRoute('product_index');
    }


    /**
     * @Route("/supprime/image/{id}", name="annonces_delete_image")
     */
    public function deleteImage(Images $image, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])) {
            $nom = $image->getName();
            unlink($this->getParameter('images_directory').'/'.$nom);
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }

    /**
     * @Route("/desactiver/{id}", name="product_descactiver")
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function desctiver(Product $product)
    {
        if ($product->getActiver()==true) {
            $entityManager = $this->getDoctrine()->getManager();
            $product->setActiver(false);
            $entityManager->persist($product);
            $entityManager->flush();
        }
        return $this->redirectToRoute('product_index');
    }

    /**
     * @Route("/activer/{id}", name="product_cactiver")
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function activer(Product $product)
    {
        if ($product->getActiver()==false) {
            $entityManager = $this->getDoctrine()->getManager();
            $product->setActiver(true);
            $entityManager->persist($product);
            $entityManager->flush();
        }
        return $this->redirectToRoute('product_index');
    }
}
