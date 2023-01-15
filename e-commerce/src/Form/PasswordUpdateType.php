<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, ['label'=>false,'attr'=>[

                'placholder'=>'Entrez votre mot de passe',
                'class'=>'form-control ',
            ]])
            ->add('newPassword', PasswordType::class, ['label'=>false,'attr'=>[

                'placholder'=>'Entrez votre nouveau mot de passe',
                'class'=>'form-control',

            ]])

            ->add('confirmPassword', PasswordType::class, ['label'=>false,'attr'=>[
                'placholder'=>'Confirmez votre nouveau mot de passe',
                'class'=>'form-control ',
            ]])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
