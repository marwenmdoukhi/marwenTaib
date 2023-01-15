<?php

namespace App\Form\Configuration;

use App\Repository\StyleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductConfigurationParfumeType extends AbstractType
{
    /**
     * @param StyleRepository $styleRepository
     */
    public function __construct(StyleRepository  $styleRepository)
    {
        $this->styleRepository = $styleRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('volume', EntityType::class, [
                'class'=>"App\Entity\Volume",
                'required'=>false
            ])
            ->add('fragranceDeParfum', EntityType::class, [
                'class'=>"App\Entity\FragranceDeParfum",
                'required'=>false
            ])
            ->add('typeDeMaquillage', EntityType::class, [
                'class'=>"App\Entity\TypeDeMaquillage",
                'required'=>false
            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Product',
        ]);
    }
}
