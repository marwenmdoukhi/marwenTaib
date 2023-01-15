<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label'=>'Prenom',
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=>'Prenom',
                    'valeur'=>'prenom',
                    'label'=>false

                ]
            ])
            ->add('lastName', TextType::class, [
                'label'=>'Nom',

                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=>'Nom'

                ]
            ])
            ->add('adress', TextType::class, [
                'label'=>'Adresse',
                    'attr'=>[
                        'class'=>'form-control',
                        'placeholder'=>'Nom'
                    ]

            ])
            ->add(
                'tel',
                TextType::class,
                [
                'label'=>'Numéro téléphone',
                    'attr'=>[
                        'class'=>'form-control ',
                        'placeholder'=>'Nom'
                    ]
                ]
            )
            ->add(
                'codepostal',
                TextType::class,
                [
                    'label'=>false,
                    'attr'=>[
                        'class'=>'form-control ',
                        'placeholder'=>'Nom'
                    ]
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
