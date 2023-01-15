<?php

namespace App\Service\SubBoutique;

use App\Data\SubBoutique\SearchSubAccessoire;
use App\Data\SubBoutique\SearchSubHorlogerie;
use App\Data\SubBoutique\SearchSubOptique;
use App\Data\SubBoutique\SearchSubParfumerie;
use App\Form\SubBoutique\SearchSubAccessoiresForm;
use App\Form\SubBoutique\SearchSubHorlogrieForm;
use App\Form\SubBoutique\SearchSubOptiqueForm;
use App\Form\SubBoutique\SearchSubParfumerieForm;
use App\Repository\ProductRepository;
use App\Repository\SubSubCategoriesRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class SubBoutiqueService
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;
    /**
     * @var SubSubCategoriesRepository
     */
    private $subcategoryRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param FormFactoryInterface $formFactory
     * @param SubSubCategoriesRepository $subcategoryRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(FormFactoryInterface $formFactory, SubSubCategoriesRepository $subcategoryRepository, ProductRepository $productRepository)
    {
        $this->formFactory = $formFactory;
        $this->subcategoryRepository = $subcategoryRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param Request $request
     * @param $slug
     * @return array
     */
    public function lunettes(Request  $request, $slug): array
    {
        $data=new SearchSubOptique();
        $template="lunettes";
        $data->page=$request->get('page', 1);
        $form=$this->formFactory->create(SearchSubOptiqueForm::class, $data, [
            'slug' => $slug]);
        [$min,$max]=$this->productRepository->findMinMaxfindBySubBoutiqueOptique($data, $slug);
        $form->handleRequest($request);
        $products= $this->productRepository->findBySubBoutiqueOptique($data, $slug);
        $subCategories=$this->subcategoryRepository->findsub($slug);
        return ([
            'products'=>$products,
            'form'=>$form->createView(),
            'min'=>$min,
            'max'=>$max,
            'subCategories'=>$subCategories,
            'template'=>$template
        ]);
    }

    /**
     * @param Request $request
     * @param $slug
     * @return array
     */
    public function cateHorlogrie(Request  $request, $slug): array
    {
        $data=new SearchSubHorlogerie();
        $template="subHorlogrie";
        $data->page=$request->get('page', 1);
        $form=$this->formFactory->create(SearchSubHorlogrieForm::class, $data, [
            'slug' => $slug]);
        [$min,$max]=$this->productRepository->findMinMaxfindBySubBoutiqueHorlogerie($data, $slug);
        $form->handleRequest($request);
        $products=$this->productRepository->findSearchBoutiqueSubHorlogerie($data, $slug);
        return ([
            'products'=>$products,
            'form'=>$form->createView(),
            'min'=>$min,
            'max'=>$max,
            'template'=>$template
        ]);
    }

    /**
     * @param Request $request
     * @param $slug
     * @return array
     */
    public function cateParfumerie(Request  $request, $slug): array
    {
        $data=new SearchSubParfumerie();
        $template="subParfumerie";
        $data->page=$request->get('page', 1);
        $form=$this->formFactory->create(SearchSubParfumerieForm::class, $data, [
            'slug' => $slug]);
        [$min,$max]=$this->productRepository->findMinMaxfindBySubBoutiqueParfumerie($data, $slug);
        $form->handleRequest($request);
        $products=$this->productRepository->findSearchBoutiqueSubParfumerie($data, $slug);
        $subCategories=$this->subcategoryRepository->findsub($slug);
        return ([
            'products'=>$products,
            'form'=>$form->createView(),
            'min'=>$min,
            'max'=>$max,
            'subCategories'=>$subCategories,
            'template'=>$template
        ]);
    }

    /**
     * @param Request $request
     * @param $slug
     * @return array
     */
    public function catAccessoires(Request  $request, $slug): array
    {
        $data=new SearchSubAccessoire();
        $template="subAccessoires";
        $data->page=$request->get('page', 1);
        $form=$this->formFactory->create(SearchSubAccessoiresForm::class, $data, [
            'slug' => $slug]);
        [$min,$max]=$this->productRepository->findMinMaxfindBySubBoutiqueAccessoires($data, $slug);
        $form->handleRequest($request);
        $products=$this->productRepository->findSearchBoutiqueSubAccessoires($data, $slug);
        return ([
            'products'=>$products,
            'form'=>$form->createView(),
            'min'=>$min,
            'max'=>$max,
            'template'=>$template
        ]);
    }
}
