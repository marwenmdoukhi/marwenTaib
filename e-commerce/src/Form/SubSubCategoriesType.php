<?php

namespace App\Form;

use App\Entity\SubSubCategories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubSubCategoriesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('subCategories')
            ->add('picture', FileType::class, [
                'required'=>false,
                'mapped' => false,
                'label'=>"photo",
                'attr'=>[
                    'class'=>
                        'custom-file-container__custom-file__custom-file-input']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SubSubCategories::class,
        ]);
    }
}
