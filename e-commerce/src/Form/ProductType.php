<?php

namespace App\Form;

use App\Entity\Subcategory;
use App\Repository\CategoryRepository;
use App\Repository\SubcategoryRepository;
use App\Repository\MatierBraceletRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    private $repository;

    /**
     * @var Subcategory
     */
    private $subcategory;

    /**
     * @param CategoryRepository $repository
     * @param MatierBraceletRepository $tagRepository
     */
    public function __construct(CategoryRepository $repository, MatierBraceletRepository $tagRepository, SubcategoryRepository $subcategory)
    {
        $this->categories = $repository;
        $this->tagRepository = $tagRepository;
        $this->subcategory = $subcategory;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('price')
            ->add('refrence')
            ->add('sex', ChoiceType::class, [
                'choices'  => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                    'UniSex'=>'UniSex'
                ],
            ])
            ->add('description', CKEditorType::class)
            ->add('easeOfPayment', CKEditorType::class)

            ->add('imageFile', FileType::class, [
                'required'=>false,
                'attr'=>[
                    'class'=>
                        'custom-file-container__custom-file__custom-file-input']
            ])
            ->add('promo')
            ->add('pricePromo')
            ->add('categories')
            ->add('subCategory', EntityType::class, [
                'class'=>'App\Entity\Subcategory',
                'choice_label'=>'name'
            ])
            ->add('subSubCategories', EntityType::class, [
                'class'=>'App\Entity\SubSubCategories',
                'choice_label'=>'name',
                'required'=>false,
            ])

            ->add('marque')

            ->add('images', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])

            ->add('linkYoutube')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Product',
        ]);
    }
}
