<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Origine;
use App\Entity\Category;

use App\Entity\Restaurant;
use App\Form\ImageUploadType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

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
                'attr' => ['class'=>'full-form-input', 'placeholder' => $this->translator->trans('Mon nom de resto ici')],
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
                ],
                'constraints' => [new Regex(pattern:"/^[a-zA-Z]+\s?.{0,70}[,]{1}\s?[0-9]{1,4}\s?[a-zA-Z]{0,2}[0-9]{0,3}$/",message:$this->translator->trans('Insérrer une adresse valide (chemin de la montagne,24/chemin de la montagne,24b)'))]
            ])

            ->add('phone',TextType::class,[
                'label'=>false,
                'attr' =>[
                    'placeholder' => '+3200000000'
                ],
                'constraints' =>[
                    new Regex( pattern:'/^((\+|00)(32|33)\s?|0)4(60|[789]\d)(\s?\d{2})/',message:$this->translator->trans('veuillez insérer un numéro valide, exemple +32477000000 ou 0477000000'))
                ]
            ])
            
            ->add('city',EntityType::class,[
                'class' => City::class,
                'choice_label' => 'localite',
                'placeholder' => $this->translator->trans('choisisez votre ville'),
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
                        'placeholder' => $this->translator->trans('Catégorie du restaurant')
                    ],
                    'constraints' => [new Count(min:1 , minMessage: $this->translator->trans('Vous devez sélectioner une catégorie'),max:2, maxMessage: $this->translator->trans('Vous pouvez sélectionner deux catégories maximum'))]
                    ])
                    ->add('origine',EntityType::class,
                    [
                        'class' => Origine::class,
                        'choice_label' =>'name',
                        'expanded' => false,
                        'multiple' => true,
                        'autocomplete' => true,
                        'label'=>false,
                        'attr' =>
                        [      
                            'placeholder' => $this->translator->trans('Origine de la cuisine')
                        ],
                        'constraints' => [new Count(min:1 , minMessage: $this->translator->trans('Vous devez sélectioner un type de cuisine'),max:2, maxMessage: $this->translator->trans('Vous pouvez sélectionner deux types de cuisine maximum'))], 
                    ])
                    ->add('images',CollectionType::class,[
                        'entry_type' => ImageUploadType::class,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'required' => false,
                        'mapped' => false,
                        'label' =>  false,
                     'attr' => [
                        'class' => 'container-grid-all'  ,'always_empty' =>false 
                     ],
                     'constraints' => [new Count(max:4,maxMessage:$this->translator->trans('vous avez dépassé le nombre d`\image autorisées'))], 
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
            