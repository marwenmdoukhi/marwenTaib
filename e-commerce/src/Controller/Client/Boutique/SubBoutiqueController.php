<?php

namespace App\Controller\Client\Boutique;

use App\Service\ValidateURL\ValidateUrlService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubBoutiqueController extends AbstractController
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
     * @Route("/{name}/{slug}", name="subBoutique")
     * @param Request $request
     * @param $slug
     * @param $name
     * @return RedirectResponse|Response
     */
    public function __invoke(Request $request, $slug, $name)
    {
        $reponse=$this->boutiqueService->subBoutique($request, $slug, $name);
        $template=$reponse['template'];
        return $this->render(
            'client/subBoutique/'.$template.'/index.html.twig',
            $reponse
        );
    }
}
