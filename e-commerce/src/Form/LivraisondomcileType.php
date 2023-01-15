<?php

namespace App\Form;

use App\Entity\Livraisondomcile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivraisondomcileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Nom', TextType::class, [
                'label'=>false,
                'attr'=>[
                    'class'=>'form-control lesinputs',
                    'placeholder'=>'Nom',



                ]
            ])
            ->add('prenom', TextType::class, [
                'label'=>false,
                'attr'=>[
                    'class'=>'form-control lesinputs',
                    'placeholder'=>'Prenom',
                ]
            ])
            ->add('numtelprin', TextType::class, [
                'label'=>false,
                'attr'=>[
                    'class'=>'form-control lesinputs',
                    'placeholder'=>'Numéro de téléphone principale',
                ]
            ])
            ->add('numteldeux', TextType::class, [
                'label'=>false,
                'attr'=>[
                    'class'=>'form-control lesinputs',
                    'placeholder'=>'Deuxième numéro de téléphone
',
                ]
            ])

            ->add('adresse', TextType::class, [
        'label'=>false,
        'attr'=>[
            'class'=>'form-control lesinputs',
            'placeholder'=>'Adresse exacte de livraison 
',
        ]
    ])
            ->add('horaires', TextType::class, [
                'label'=>false,
                'attr'=>[
                    'class'=>'form-control lesinputs',
                    'placeholder'=>'Heure:Minute  
',
                ]
            ])
            ->add(
                'typedepaiment',
                ChoiceType::class,
                ['choices'  =>
                    [
                        'Paiement à la livraison' => '1',
                        'Payer par paymee' => "2",
                    ],
                    'multiple'=>false,
                    'expanded'=>true,
                    'label'=>false,
                ]
            )
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'flex-c-m sizefull bg1 bo-rad-23 hov1 s-text1 trans-0-4 size15 trans-0-4 d-flex'
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Livraisondomcile::class,
        ]);
    }
}
