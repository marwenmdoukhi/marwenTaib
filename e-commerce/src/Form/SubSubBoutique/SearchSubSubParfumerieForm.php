<?php

namespace  App\Form\SubSubBoutique;

use App\Data\SubSubBoutique\SearchSubSubParfumerie;
use App\Entity\Marque;
use App\Entity\TypeDeMaquillage;
use App\Entity\Volume;
use App\Repository\MarqueRepository;
use App\Repository\StyleRepository;
use App\Repository\SubcategoryRepository;
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

class SearchSubSubParfumerieForm extends AbstractType
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
     * @param MarqueRepository $marquerespositry
     * @param VolumeRepository $volumeRepository
     * @param TypeDeMaquillageRepository $typeDeMaquillageRepository
     */
    public function __construct(MarqueRepository $marquerespositry, VolumeRepository  $volumeRepository, TypeDeMaquillageRepository  $typeDeMaquillageRepository)
    {
        $this->marquerespositry = $marquerespositry;
        $this->volumeRepository = $volumeRepository;
        $this->typeDeMaquillageRepository = $typeDeMaquillageRepository;
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
            ->add('marque', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Marque::class,
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->marquerespositry->findBySubMarqueSlug($options['name']),
            ])

            ->add('volume', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Volume::class,
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->volumeRepository->findBySubVolumeSlug($options['name']),
            ])

            ->add('type', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => TypeDeMaquillage::class,
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->typeDeMaquillageRepository->findBySubTypeDeMaquillageSlug($options['name']),
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
            'data_class' => SearchSubSubParfumerie::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'name'=>null
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
