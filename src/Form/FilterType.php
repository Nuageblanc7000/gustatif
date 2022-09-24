<?php

namespace App\Form;

use App\Entity\Origine;
use App\Data\DataFilter;
use App\Entity\Category;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('s',TextType::class,
                [
                    'attr' =>
                    [
                        'placeholder' => 'entrer une ville',
                        'autocomplete' => 'off',
                    ],
                    'required' => false,
                ]
            )
            ->add('categories',EntityType::class,
                [
                    'class' => Category::class,
                    'choice_label' => 'name',
                    'label' => false,
                    'expanded' => true,
                    'multiple' => true,
                    'required' => false,
                ]
            )
            ->add('t',EntityType::class,
                    [
                        'class' => Origine::class,
                        'choice_label' => 'name',
                        'expanded' => false,
                        'required' => false,
                        'placeholder' => 'Choix de la cuisne'
                    ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DataFilter::class,
            'method' => "GET",
            'csrf_protection' => false
        ]);
    }
    /**
     * permet de changer le préfix dans l'url pour ne pas avoir des tableaux
     *
     * @return string
     */
    public function getBlockPrefix()
    {
        return '';
        
    }
}
