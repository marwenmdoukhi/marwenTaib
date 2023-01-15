<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    /**
     * @Route("/sitemap.xml", name="sitemap", defaults={"_format"="xml"})
     */
    public function index(Request  $request): Response
    {
        $hostname = $request->getSchemeAndHttpHost();
        $urls = [];
        $urls[] = ['loc' => $this->generateUrl('home')];
        $urls[] = ['loc' => $this->generateUrl('contact')];
        $urls[] = ['loc' => $this->generateUrl('account_register')];
        $urls[] = ['loc' => $this->generateUrl('account_login')];
        $urls[] = ['loc' => $this->generateUrl('boutiquesolde')];
        foreach ($this->getDoctrine()->getRepository(Product::class)->findAll() as $product) {
            $images=[
                'image'=>'/uploads/images/products'.$product->getFilename(),
                'loc'=>'/media/cache/detail/uploads/images/products/'.$product->getFilename(),
                'title'=>$product->getName(),
            ];
            $urls[]=[
              'loc'=>$this->generateUrl('product', [
                  'slug'=>$product->getSlug(),
                  'category'=>$product->getCategories()->getName(),
              ]),
                'image'=>$images,
                'lastmod'=>$product->getCreatedAt()->format('Y-m-d'),
                'categories'=>$product->getCategories()->getName(),
                'subCategories'=>$product->getSubCategory()->getName(),
                'refrence' => $product->getRefrence(),
                'marque'=>$product->getMarque()->getName(),
                'description'=>$product->getDescription(),
                'sex'=>$product->getSex(),
            ];
        }
        foreach ($this->getDoctrine()->getRepository(Product::class)->xmlSolde() as $product) {
            $images=[
                'image'=>'/uploads/images/products'.$product->getFilename(),
                'loc'=>'/media/cache/detail/uploads/images/products/'.$product->getFilename(),
                'title'=>$product->getName(),
            ];
            $urls[]=[
                'loc'=>$this->generateUrl('boutiquesolde', [
                    'slug'=>$product->getSlug(),
                    'category'=>$product->getCategories()->getName(),
                ]),
                'image'=>$images,
                'lastmod'=>$product->getCreatedAt()->format('Y-m-d'),
                'categories'=>$product->getCategories()->getName(),
                'subCategories'=>$product->getSubCategory()->getName(),
                'refrence' => $product->getRefrence(),
                'marque'=>$product->getMarque()->getName(),
                'description'=>$product->getDescription(),
                'sex'=>$product->getSex(),
            ];
        }
        foreach ($this->getDoctrine()->getRepository(Category::class)->findAll() as $categories) {
            $urls[]=[
                'loc'=>$this->generateUrl('Boutique', [
                    'slug'=>$categories->getSlug(),
                    'id'=>$categories->getId()
                ])];
        }

        $response = new Response(
            $this->renderView(
                'sitemap/index.html.twig',
                [
                    'urls' => $urls,
                    'hostname' => $hostname]
            )
        );

        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }
}
