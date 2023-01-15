<?php

namespace  App\Form\Boutique;

use App\Data\Boutique\SearchOptique;
use App\Entity\Cadre;
use App\Entity\Forme;
use App\Entity\Marque;
use App\Entity\Style;
use App\Entity\Subcategory;
use App\Form\DataTransformer\IssueToNumberTransformer;
use App\Repository\CadreRepository;
use App\Repository\FormeRepository;
use App\Repository\MarqueRepository;
use App\Repository\StyleRepository;
use App\Repository\SubcategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchOptiqueForm extends AbstractType
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
     * @var StyleRepository
     */
    private $styleRepository;

    /**
     * @var FormeRepository
     */
    private $formeRepository;

    /**
     * @var CadreRepository
     */
    private $cadreRepository;

    /**
     * @var IssueToNumberTransformer
     */
    private $issueToNumberTransformer;


    /**
     * @param MarqueRepository $marquerespositry
     * @param SubcategoryRepository $subcategoryRepository
     * @param StyleRepository $styleRepository
     * @param FormeRepository $formeRepository
     * @param CadreRepository $cadreRepository
     */
    public function __construct(
        MarqueRepository $marquerespositry,
        SubcategoryRepository $subcategoryRepository,
        StyleRepository  $styleRepository,
        FormeRepository  $formeRepository,
        CadreRepository  $cadreRepository,
        IssueToNumberTransformer $issueToNumberTransformer
    ) {
        $this->subcategoryRepository = $subcategoryRepository;
        $this->marquerespositry = $marquerespositry;
        $this->styleRepository = $styleRepository;
        $this->formeRepository = $formeRepository;
        $this->cadreRepository = $cadreRepository;

        $this->issueToNumberTransformer = $issueToNumberTransformer;
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

            ->add('style', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Style::class,
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->styleRepository->findByStyleLunetteSlug($options['slug']),
            ])

            ->add('formes', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Forme::class,
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->formeRepository->findByFormLunettSlug($options['slug']),
            ])

            ->add('cadres', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Cadre::class,
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->cadreRepository->findByFormCadreSlug($options['slug']),
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

            ->add('sex', ChoiceType::class, [
            'label' => false,
            'required' => false,
            'expanded' => true,
            'multiple' => true,
            'choices' => [
                'Homme' => 'Homme',
                'Femme' => 'Femme',
            ],
            ])
        ;
        // $builder->get('q')->addModelTransformer($this->issueToNumberTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchOptique::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'slug' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
