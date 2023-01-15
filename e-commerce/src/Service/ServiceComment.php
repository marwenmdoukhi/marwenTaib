<?php

namespace App\Service;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ServiceComment
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var UserInterface
     */
    private $user;
    /**
     * @var Security
     */
    private $security;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var RequestStack
     */
    private $request;
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var FlashBagInterface
     */
    private $flash;

    /**
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $em
     * @param Security $security
     * @param Environment $twig
     * @param RequestStack $request
     * @param CacheInterface $cache
     * @param FlashBagInterface $flash
     * @param RouterInterface $router
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em,
        Security $security,
        Environment $twig,
        RequestStack   $request,
        CacheInterface $cache,
        FlashBagInterface $flash,
        RouterInterface $router
    ) {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->user = $security->getUser();
        $this->twig = $twig;
        $this->request = $request;
        $this->cache = $cache;
        $this->flash = $flash;
        $this->router = $router;
    }

    /**
     * @param $slug
     * @param $category
     * @param ProductRepository $productRepository
     * @return RedirectResponse|Response
     * @throws InvalidArgumentException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function product($slug,$id, $category, ProductRepository $productRepository)
    {
        $comment = new Comment();
        $form=$this->formFactory->create(CommentType::class, $comment);
        $form->handleRequest($this->request->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAd($productRepository->findbyslug($slug,$id))
                ->setAuthor($this->user);
            $this->em->persist($comment);
            $this->em->flush();
            $this->flash->add('success', "Merci pour votre Avis");
            return new RedirectResponse($this->router->generate('product', ['slug'=>$slug,'id'=>$id]));
        }
        return new Response($this->twig->render('client/produit/produit.html.twig', [
            'form' => $form->createView(),
            "product" => $productRepository->findbyslug($slug,$id),
            'productAsc'=>$productRepository->productAsec($category),
            'productDesc'=>$productRepository->productDesc($category),
            'productInCategory'=>$productRepository->productInCategory($category),

        ]));
    }
}
