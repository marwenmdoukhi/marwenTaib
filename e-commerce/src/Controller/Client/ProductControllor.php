<?php

namespace App\Controller\Client;

use App\Repository\ProductRepository;
use App\Service\ServiceComment;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ProductControllor extends AbstractController
{
    /**
     * @var ServiceComment
     */
    private $comment;

    public function __construct(ServiceComment $comment)
    {
        $this->comment = $comment;
    }
    /**
     * @Route("/product/{id}-{category}/{slug}", name="product")
     * @param $slug
     * @param ProductRepository $productRepository
     * @return Response
     * @throws InvalidArgumentException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke($slug,$id, $category, ProductRepository $productRepository)
    {
        $response =  $this->comment->product($slug,$id, $category, $productRepository);
        $response->setPublic();
        $response->setMaxAge(3600);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        return $response;
    }
}
