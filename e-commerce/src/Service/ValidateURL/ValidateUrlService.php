<?php

namespace App\Service\ValidateURL;

use App\Constante\Verif;
use App\Repository\ProductRepository;
use App\Service\Boutique\BoutiqueService;
use App\Service\SubBoutique\SubBoutiqueService;
use App\Service\SubSubBoutique\SubSubBoutiqueService;
use Symfony\Component\HttpFoundation\Request;

class ValidateUrlService
{
    /**
     * @var BoutiqueService
     */
    private $boutiqueService;

    /**
     * @var SubBoutiqueService
     */
    private $subBoutiqueService;

    /**
     * @var SubSubBoutiqueService
     */
    private $subsubBoutiqueService;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param BoutiqueService $boutiqueService
     * @param SubBoutiqueService $subBoutiqueService
     * @param SubSubBoutiqueService $subsubBoutiqueService
     * @param ProductRepository $productRepository
     */
    public function __construct(BoutiqueService $boutiqueService, SubBoutiqueService $subBoutiqueService, SubSubBoutiqueService $subsubBoutiqueService, ProductRepository $productRepository)
    {
        $this->boutiqueService = $boutiqueService;
        $this->subBoutiqueService = $subBoutiqueService;
        $this->subsubBoutiqueService = $subsubBoutiqueService;
        $this->productRepository = $productRepository;
    }

    public function boutique(Request $request, $slug)
    {
        if (preg_match('/'.Verif::boutiqueOptique.'/', $request->getPathInfo())) {
            return $this->boutiqueService->optique($request, $slug);
        }
        if (preg_match('/'.Verif::boutiqueHorlogerie.'/', $request->getPathInfo())) {
            return $this->boutiqueService->horlogrie($request, $slug);
        }
        if (preg_match('/'.Verif::boutiqueOccasion.'/', $request->getPathInfo())) {
            return $this->boutiqueService->occasion($request, $slug);
        }
        if (preg_match('/'.Verif::boutiqueParfumerie.'/', $request->getPathInfo())) {
            return $this->boutiqueService->parfumerie($request, $slug);
        }
        if (preg_match('/'.Verif::boutiqueAccessoires.'/', $request->getPathInfo())) {
            return $this->boutiqueService->accessoires($request, $slug);
        }
    }

    public function subBoutique(Request $request, $slug, $name)
    {
        if (preg_match('/'.Verif::subBoutiqueLunette.'/', $request->getPathInfo())) {
            return $this->subBoutiqueService->lunettes($request, $slug, $name);
        }
        if (preg_match('/'.Verif::subBoutiqueHorlogerie.'/', $request->getPathInfo())) {
            return $this->subBoutiqueService->cateHorlogrie($request, $slug, $name);
        }
        if (preg_match('/'.Verif::boutiqueSubParfumerie.'/', $request->getPathInfo())) {
            return $this->subBoutiqueService->cateParfumerie($request, $slug, $name);
        }
        if (preg_match('/'.Verif::boutiqueSubAccessoires.'/', $request->getPathInfo())) {
            return $this->subBoutiqueService->catAccessoires($request, $slug, $name);
        }
    }
    public function subsubBoutique(Request $request, $slug, $name)
    {
        if (preg_match('/'.Verif::subSubBoutiqueLunette.'/', $request->getPathInfo())) {
            return $this->subsubBoutiqueService->sublunettes($request, $slug, $name);
        }
        if (preg_match('/'.Verif::subSubBoutiqueParfumerie.'/', $request->getPathInfo())) {
            return $this->subsubBoutiqueService->subParfumerie($request, $slug, $name);
        }
    }
}
