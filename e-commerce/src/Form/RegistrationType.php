<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'attr'=>[
                    'class'=>'form-control lesinputs',
                    'placeholder'=>'Prenom',
                    'valeur'=>'prenom'

                ]
            ])
            ->add('lastName', TextType::class, [
                'attr'=>[
                    'class'=>'form-control lesinputs',
                    'placeholder'=>'Nom'

                ]
            ])
            ->add('email', TextType::class, [
                'label'=>false,
                'attr'=>[
                    'class'=>'form-control lesinputs',
                    'placeholder'=>'Email'


                ]
            ])
            ->add('hash', PasswordType::class, [
                'attr'=>[
                    'placeholder'=>'Mot de passe',
                    'class'=>'form-control lesinputs'

                ]
            ])
            ->add('adress', TextType::class, [
                'attr'=>[
                    'class'=>'form-control lesinputs',
                    'placeholder'=>'Adresse',

                ]
            ])

            ->add('tel', TelType::class, [
                'attr'=>[
                    'class'=>'form-control lesinputs',
                    'placeholder'=>'N° De Tel',

                ]
            ])
            ->add('codepostal', TelType::class, [
                'attr'=>[
                    'class'=>'form-control lesinputs',
                    'placeholder'=>'Code Postal',

                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
