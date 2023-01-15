<?php

namespace App\Service\Boutique;

use App\Data\Boutique\Searchaccessoire;
use App\Data\Boutique\SearchOccasion;
use App\Data\Boutique\SearchOptique;
use App\Data\Boutique\SearcHorlogerie;
use App\Data\Boutique\SearchParfumerie;
use App\Form\Boutique\SearchAccessoiresForm;
use App\Form\Boutique\SearchHorlogrieForm;
use App\Form\Boutique\SearchOccasionForm;
use App\Form\Boutique\SearchOptiqueForm;
use App\Form\Boutique\SearchParfumerieForm;
use App\Repository\ProductRepository;
use App\Repository\SubcategoryRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class BoutiqueService
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var SubcategoryRepository
     */
    private $subcategoryRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param FormFactoryInterface $formFactory
     * @param SubcategoryRepository $subcategoryRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(FormFactoryInterface $formFactory, SubcategoryRepository $subcategoryRepository, ProductRepository $productRepository)
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
    public function optique(Request $request, $slug): array
    {
        $data=new SearchOptique();
        $data->page=$request->get('page', 1);
        $form=$this->formFactory->create(SearchOptiqueForm::class, $data, [
            'slug' => $slug]);
        [$min,$max]=$this->productRepository->findMinMaxBoutiqueOptique($data, $slug);
        $form->handleRequest($request);
        $products=$this->productRepository->findSearchBoutiqueOptique($data, $slug);
        $subCategories=$this->subcategoryRepository->findBySlug($slug);
        return ([
            'products'=>$products,
            'form'=>$form->createView(),
            'min'=>$min,
            'max'=>$max,
            'subCategories'=>$subCategories
        ]);
    }

    /**
     * @param Request $request
     * @param $slug
     * @return array
     */
    public function horlogrie(Request $request, $slug)
    {
        $data=new SearcHorlogerie();
        $data->page=$request->get('page', 1);
        $form=$this->formFactory->create(SearchHorlogrieForm::class, $data, [
            'slug' => $slug]);
        [$min,$max]=$this->productRepository->findMinMaxBoutiqueHorlogrie($data, $slug);
        $form->handleRequest($request);
        $products=$this->productRepository->findSearchBoutiqueHorlogerie($data, $slug);
        $subCategories=$this->subcategoryRepository->findBySlug($slug);
        return ([
            'products'=>$products,
            'form'=>$form->createView(),
            'min'=>$min,
            'max'=>$max,
            'subCategories'=>$subCategories
        ]);
    }

    /**
     * @param Request $request
     * @param $slug
     * @return array
     */
    public function occasion(Request $request, $slug)
    {
        $data=new SearchOccasion();
        $data->page=$request->get('page', 1);
        $form=$this->formFactory->create(SearchOccasionForm::class, $data);
        [$min,$max]=$this->productRepository->findMinMaxBoutiqueOccasion($data, $slug);
        $form->handleRequest($request);
        $products=$this->productRepository->findSearchBoutiqueOccasion($data, $slug);
        return ([
            'products'=>$products,
            'form'=>$form->createView(),
            'min'=>$min,
            'max'=>$max,
        ]);
    }

    /**
     * @param Request $request
     * @param $slug
     * @return array
     */
    public function parfumerie(Request $request, $slug)
    {
        $data=new SearchParfumerie();
        $data->page=$request->get('page', 1);
        $form=$this->formFactory->create(SearchParfumerieForm::class, $data, [
            'slug' => $slug]);
        [$min,$max]=$this->productRepository->findMinMaxBoutiqueParfumerie($data, $slug);
        $form->handleRequest($request);
        $products=$this->productRepository->findSearchBoutiqueParfumerie($data, $slug);
        $subCategories=$this->subcategoryRepository->findBySlug($slug);
        return ([
            'products'=>$products,
            'form'=>$form->createView(),
            'min'=>$min,
            'max'=>$max,
            'subCategories'=>$subCategories
        ]);
    }

    /**
     * @param Request $request
     * @param $slug
     * @return array
     */
    public function accessoires(Request $request, $slug)
    {
        $data=new Searchaccessoire();
        $data->page=$request->get('page', 1);
        $form=$this->formFactory->create(SearchAccessoiresForm::class, $data, [
            'slug' => $slug]);
        [$min,$max]=$this->productRepository->findMinMaxBoutiqueAccessoire($data, $slug);
        $form->handleRequest($request);
        $products=$this->productRepository->findSearchBoutiqueAccessoire($data, $slug);
        $subCategories=$this->subcategoryRepository->findBySlug($slug);
        return ([
            'products'=>$products,
            'form'=>$form->createView(),
            'min'=>$min,
            'max'=>$max,
            'subCategories'=>$subCategories
        ]);
    }
}
