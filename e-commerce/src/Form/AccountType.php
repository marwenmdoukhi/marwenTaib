<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label'=>'Prenom',
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
                'attr'=>[
                    'class'=>'form-control lesinputs',
                    'placeholder'=>'Email'

                ]
            ])
            ->add('picture', FileType::class, [
                'required'=>false,
                'mapped' => false,
                'label'=>"photo",
                'attr'=>[
                    'class'=>
                        'custom-file-container__custom-file__custom-file-input']
            ])            //->add('hash')
            ->add('adress')
            ->add('tel')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
