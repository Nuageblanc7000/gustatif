<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Origine;
use App\Entity\Category;

use App\Entity\Restaurant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RestoType extends AbstractType
{
    public function __construct(public TranslatorInterface $translator)
    {
        
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,
            [
                'label'=>false,
                'attr' => ['class'=>'full-form-input', 'placeholder' => 'Mon nom de resto ici'],
                'row_attr' => 
                ['class' => 'full-form-input'],
                ],
            )
            ->add('description',TextareaType::class,[
                'label'=>false,
                'row_attr' => ['class' => 'full-form-input'],
                'attr' => [ 'placeholder' =>  $this->translator->trans("Aujourd’hui, notre chef vous propose une cuisine traditionnelle raffinée, élaborée avec des produits frais, de saison dans un décor feutré, en plein cœur du 15ème arrondissement de Paris. Une grande terrasse vous accueille en été et en hiver tout au long de la journée. Un must : nos plateaux de fruits de mer ! L\'Armandie a le plaisir de vous proposer une large sélection de fruits de mer frais, servis sur place ou à emporter.

                    "
                    )]

            ])
            ->add('adress',TextType::class ,[
                'label'=>false,
                'attr' =>[
                    'placeholder' => 'Rue charle,23'
                ]
            ])

            ->add('phone',TextType::class,[
                'label'=>false,
                'attr' =>[
                    'placeholder' => '+3200000000'
                ]
            ])
            ->add('file',FileType::class,[
                'mapped' => false,
            ])

            ->add('city',EntityType::class,[
                'class' => City::class,
                'choice_label' => 'localite',
                'placeholder' => 'choisisez votre ville',
                'autocomplete' => true,
                'label'=>false,
            ])
            ->add('category',EntityType::class,[
                'class' => Category::class,
                'choice_label' =>'name',
                'expanded' => false,
                
                'multiple' => true,
                'autocomplete' => true,
                'label'=>false,
                'attr' =>[
                    'placeholder' => 'Catégorie du restaurant'
                ],
            ])
            ->add('origine',EntityType::class,[
                'class' => Origine::class,
                'choice_label' =>'name',
                'expanded' => false,
                'multiple' => true,
                'autocomplete' => true,
                'label'=>false,
                'attr' =>[

                    'placeholder' => 'Origine de la cuisine'
                    ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Restaurant::class,
        ]);
    }
}