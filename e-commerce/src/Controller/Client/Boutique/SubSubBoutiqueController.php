<?php

namespace App\Controller\Client\Boutique;

use App\Service\ValidateURL\ValidateUrlService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubSubBoutiqueController extends AbstractController
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
     * @Route("/boutique-{boutique}/{name}/{slug}", name="subsubBoutique")
     * @param Request $request
     * @param $slug
     * @param $name
     * @return Response
     */
    public function __invoke(Request $request, $slug, $name)
    {
        $reponse= $this->boutiqueService->subsubBoutique($request, $slug, $name);
        $template=$reponse['template'];
        return $this->render(
            'client/subSubBoutique/'.$template.'/index.html.twig',
            $reponse
        );
    }
}
