<?php

namespace App\Controller\Client\Boutique;

use App\Entity\Category;
use App\Service\ValidateURL\ValidateUrlService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BoutiqueController extends AbstractController
{
    /**
     * @var ValidateUrlService
     */
    private $boutiqueService;

    /**
     * @param ValidateUrlService $boutiqueService
     */
    public function __construct(ValidateUrlService $boutiqueService)
    {
        $this->boutiqueService = $boutiqueService;
    }

    /**
     *@Route("/boutique/{slug}", name="Boutique")
     * @param Request $request
     * @param String $slug
     * @param Category $category
     * @return RedirectResponse|Response
     */
    public function __invoke(Request $request, String $slug, Category $category)
    {
        if ($category->getActiver() === false) {
            return $this->redirectToRoute('home');
        }
        return $this->render(
            'client/boutique/'.$slug.'/index.html.twig',
            $this->boutiqueService->boutique($request, $slug)
        );
    }
}
