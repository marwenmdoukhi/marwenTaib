<?php

namespace  App\Form\Boutique;

use App\Data\Boutique\SearcHorlogerie;
use App\Entity\FormeDuCadran;
use App\Entity\Marque;
use App\Entity\MatierBracelet;
use App\Entity\Style;
use App\Entity\Subcategory;
use App\Entity\TypeDuMouvement;
use App\Repository\FormeDuCadranRepository;
use App\Repository\MarqueRepository;
use App\Repository\MatierBraceletRepository;
use App\Repository\StyleRepository;
use App\Repository\SubcategoryRepository;
use App\Repository\TypeDuMouvementRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchHorlogrieForm extends AbstractType
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
     * @var FormeDuCadranRepository
     */
    private $formeDuCadranRepository;
    /**
     * @var TypeDuMouvementRepository
     */
    private $mouvementRepository;
    /**
     * @var MatierBraceletRepository
     */
    private $matierBraceletRepository;

    /**
     * @param MarqueRepository $marquerespositry
     * @param SubcategoryRepository $subcategoryRepository
     * @param StyleRepository $styleRepository
     * @param FormeDuCadranRepository $formeDuCadranRepository
     * @param TypeDuMouvementRepository $mouvementRepository
     * @param MatierBraceletRepository $matierBraceletRepository
     */
    public function __construct(
        MarqueRepository $marquerespositry,
        SubcategoryRepository $subcategoryRepository,
        StyleRepository  $styleRepository,
        FormeDuCadranRepository  $formeDuCadranRepository,
        TypeDuMouvementRepository  $mouvementRepository,
        MatierBraceletRepository  $matierBraceletRepository
    ) {
        $this->subcategoryRepository = $subcategoryRepository;
        $this->marquerespositry = $marquerespositry;
        $this->styleRepository = $styleRepository;
        $this->formeDuCadranRepository = $formeDuCadranRepository;
        $this->mouvementRepository = $mouvementRepository;
        $this->matierBraceletRepository = $matierBraceletRepository;
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

            ->add('formehorlogries', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => FormeDuCadran::class,
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->formeDuCadranRepository->findByFormdeHorlogrieSlug($options['slug']),
            ])

            ->add('matierBracelet', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => MatierBracelet::class,
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->matierBraceletRepository->findByMatierBracletHorlogirieSlug($options['slug']),
            ])
            ->add('typedeMouvmemnet', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => TypeDuMouvement::class,
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->mouvementRepository->findByTypeDuMouvmentHorlogrieSlug($options['slug']),
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
            'data_class' => SearcHorlogerie::class,
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
