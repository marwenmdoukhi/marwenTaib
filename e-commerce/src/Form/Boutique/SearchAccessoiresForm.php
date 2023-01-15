<?php

namespace  App\Form\Boutique;

use App\Data\Boutique\Searchaccessoire;
use App\Data\Boutique\SearchOccasion;
use App\Entity\Marque;
use App\Entity\Subcategory;
use App\Repository\MarqueRepository;
use App\Repository\SubcategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchAccessoiresForm extends AbstractType
{
    /**
     * @var SubcategoryRepository
     */
    private $subcategoryRepository;

    /**
     * @var MarqueRepository
     */
    private $marquerespositry;


    /**
     * @param MarqueRepository $marquerespositry
     * @param SubcategoryRepository $subcategoryRepository
     */
    public function __construct(
        MarqueRepository $marquerespositry,
        SubcategoryRepository $subcategoryRepository
    ) {
        $this->subcategoryRepository = $subcategoryRepository;
        $this->marquerespositry = $marquerespositry;
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
                'trim'=> true,
                'attr' => [
                    'placeholder' => 'Rechercher Par Référence'
                ]
            ])
            ->add('subcategories', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Subcategory::class,
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->subcategoryRepository->findBySlug($options['slug']),
            ])
            ->add('marque', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Marque::class,
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->marquerespositry->findByMarqueSlug($options['slug']),
            ])


            ->add('min', NumberType::class, [
                'label' => false,
                'required' => false,
                'trim'=> true,

                'attr' => [
                    'placeholder' => 'Prix min'
                ]
            ])
            ->add('max', NumberType::class, [
                'label' => false,
                'required' => false,
                'trim'=> true,

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
        // $builder->get('q')->addModelTransformer($this->issueToNumberTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Searchaccessoire::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'slug'=>null
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
