<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('phone', TelType::class, [
                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('sujet', TextType::class, [
                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('email', EmailType::class, [
                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('message', TextareaType::class, [
                'attr'=>[
                    'class'=>'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class
        ]);
    }
}
