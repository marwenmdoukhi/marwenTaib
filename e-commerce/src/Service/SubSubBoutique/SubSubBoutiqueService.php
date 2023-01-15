<?php

namespace App\Service\SubSubBoutique;

use App\Data\SubSubBoutique\SearchSubSubOptique;
use App\Data\SubSubBoutique\SearchSubSubParfumerie;
use App\Form\SubSubBoutique\SearchSubSubOptiqueForm;
use App\Form\SubSubBoutique\SearchSubSubParfumerieForm;
use App\Repository\ProductRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;

class SubSubBoutiqueService
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var ProductRepository
     */
    private $productRepository;


    /**
     * @param FormFactoryInterface $formFactory
     * @param ProductRepository $productRepository
     */
    public function __construct(FormFactoryInterface $formFactory, ProductRepository $productRepository)
    {
        $this->formFactory = $formFactory;
        $this->productRepository = $productRepository;
    }

    public function sublunettes(Request  $request, $slug, $name)
    {
        $data=new SearchSubSubOptique();
        $template="lunettes";
        $data->page=$request->get('page', 1);
        $form=$this->formFactory->create(SearchSubSubOptiqueForm::class, $data, [
            'name'=>$name
        ]);
        [$min,$max]=$this->productRepository->findMinMaxfindBySubSubBoutiqueOptique($data, $slug, $name);
        $form->handleRequest($request);
        $products=$this->productRepository->findBySubSubBoutiqueOptique($data, $slug, $name);
        return ([
            'products'=>$products,
            'form'=>$form->createView(),
            'min'=>$min,
            'max'=>$max,
            'template'=>$template
        ]);
    }


    public function subParfumerie(Request  $request, $slug, $name)
    {
        $data=new SearchSubSubParfumerie();
        $template="subParfumerie";
        $data->page=$request->get('page', 1);
        $form=$this->formFactory->create(SearchSubSubParfumerieForm::class, $data, [
            'name'=>$name
        ]);
        [$min,$max]=$this->productRepository->findMinMaxfindBySubSubBoutiqueParfumerie($data, $slug, $name);
        $form->handleRequest($request);
        $products=$this->productRepository->findBySubSubBoutiqueParfumerie($data, $slug, $name);
        return ([
            'products'=>$products,
            'form'=>$form->createView(),
            'min'=>$min,
            'max'=>$max,
            'template'=>$template
        ]);
    }
}
