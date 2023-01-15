<?php

namespace App\Form\Configuration;

use App\Repository\StyleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductConfigurationHorlogrieType extends AbstractType
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

            ->add('style', EntityType::class, [
                'class'=>'App\Entity\Style',
                'choices' => $this->styleRepository->findByStyleLunetteSlug($options['slug']),
                'required'=>true
            ])
            ->add('verreDeMontre')
            ->add('formeDuCadran')
            ->add('matierBracelet')
            ->add('typeDuMouvement')
            ->add('fonctionMontre')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Product',
            'slug'=>"horlogerie"
        ]);
    }
}
