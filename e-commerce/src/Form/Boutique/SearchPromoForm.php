<?php

namespace  App\Form\Boutique;

use App\Data\Boutique\SearchParfumerie;
use App\Data\Boutique\SearchSolde;
use App\Entity\Category;
use App\Entity\Marque;
use App\Entity\Subcategory;
use App\Repository\CategoryRepository;
use App\Repository\MarqueRepository;
use App\Repository\SubcategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchPromoForm extends AbstractType
{
    /**
     * @var MarqueRepository
     */
    private $marquerespositry;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param MarqueRepository $marquerespositry
     */
    public function __construct(
        MarqueRepository $marquerespositry,
        CategoryRepository $categoryRepository
    ) {
        $this->marquerespositry = $marquerespositry;
        $this->categoryRepository = $categoryRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ref', TextType::class, [
                'label' => false,
                'required' => false,
                'trim'=> true,
                'attr' => [
                    'placeholder' => 'Rechercher Par Référence'
                ]
            ])
            ->add('categories', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Category::class,
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->categoryRepository->findAll(),
            ])
            ->add('marque', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Marque::class,
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->marquerespositry->findAll()
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchSolde::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
