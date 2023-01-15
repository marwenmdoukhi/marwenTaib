<?php

namespace App\Form;

use App\Entity\Subcategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label'=>'Nom'
            ])
            ->add('category', EntityType::class, [
                'class'=>'App\Entity\Category',
                'label'=>'name'
            ])
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Subcategory::class,
        ]);
    }
}
