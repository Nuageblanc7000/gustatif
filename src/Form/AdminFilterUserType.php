<?php

namespace App\Form;

use App\Data\DataAdminFIlter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminFilterUserType extends AbstractType
{
    public function __construct(public TranslatorInterface $translator){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search',TextType::class,[
                'required' => false,
                'attr' => ['class'=>'input-text-search'],
                'label' => false,
                'row_attr' => ['class'=>'input-text-search'],
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        'data_class' => DataAdminFIlter::class,
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
