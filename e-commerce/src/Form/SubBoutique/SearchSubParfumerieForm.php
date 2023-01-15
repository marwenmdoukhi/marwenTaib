<?php

namespace  App\Form\SubBoutique;

use App\Data\SubBoutique\SearchSubParfumerie;
use App\Entity\Marque;
use App\Entity\SubSubCategories;
use App\Entity\TypeDeMaquillage;
use App\Entity\Volume;
use App\Repository\MarqueRepository;
use App\Repository\SubcategoryRepository;
use App\Repository\SubSubCategoriesRepository;
use App\Repository\TypeDeMaquillageRepository;
use App\Repository\VolumeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchSubParfumerieForm extends AbstractType
{
    /**
     * @var MarqueRepository
     */
    private $marquerespositry;
    /**
     * @var VolumeRepository
     */
    private $volumeRepository;
    /**
     * @var TypeDeMaquillageRepository
     */
    private $typeDeMaquillageRepository;
    /**
     * @var SubSubCategoriesRepository
     */
    private $subSubCategoriesRepository;

    /**
     * @param MarqueRepository $marquerespositry
     * @param SubcategoryRepository $subcategoryRepository
     * @param VolumeRepository $volumeRepository
     * @param TypeDeMaquillageRepository $typeDeMaquillageRepository
     * @param SubSubCategoriesRepository $subSubCategoriesRepository
     */
    public function __construct(
        MarqueRepository $marquerespositry,
        SubcategoryRepository $subcategoryRepository,
        VolumeRepository  $volumeRepository,
        TypeDeMaquillageRepository  $typeDeMaquillageRepository,
        SubSubCategoriesRepository $subSubCategoriesRepository
    ) {
        $this->subcategoryRepository = $subcategoryRepository;
        $this->marquerespositry = $marquerespositry;
        $this->volumeRepository = $volumeRepository;
        $this->typeDeMaquillageRepository = $typeDeMaquillageRepository;
        $this->subSubCategoriesRepository = $subSubCategoriesRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('q', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'SearchLunettes...',
                    'class' => 'form-control'
                ]
            ])
            ->add('ref', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher Par Référence'
                ]
            ])
            ->add('subcategories', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => SubSubCategories::class,
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->subSubCategoriesRepository->findBySubSubCategories($options['slug']),
            ])
            ->add('marque', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Marque::class,
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->marquerespositry->findBySubMarqueSlug($options['slug']),
            ])

            ->add('volume', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Volume::class,
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->volumeRepository->findBySubVolumeSlug($options['slug']),
            ])

            ->add('type', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => TypeDeMaquillage::class,
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->typeDeMaquillageRepository->findBySubTypeDeMaquillageSlug($options['slug']),
            ])

            ->add('min', NumberType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prix min'
                ]
            ])
            ->add('max', NumberType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prix max'
                ]
            ])
            ->add('promo', CheckboxType::class, [
                'label' => 'En promotion',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input '
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchSubParfumerie::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'slug' => null
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
