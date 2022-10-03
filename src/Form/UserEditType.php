<?php

namespace App\Form;

use App\Entity\City;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\NotNull;

class UserEditType extends AbstractType
{
    public function __construct(public TranslatorInterface $translator)
    {
        
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,[

            ])
            ->add('pseudo',TextType::class,[
            ])
            ->add('adress',TextType::class,[
                'required' => false,
                'attr' => [
                    'placeholder' => 'Chemin de la louvière,24',
                ],
                'constraints' => [
                    new Regex(pattern:"/^[a-zA-Z]+\s?.{0,70}[,]{1}\s?[0-9]{1,4}\s?[a-zA-Z]{0,2}[0-9]{0,3}$/",message: $this->translator->trans('Adresse valide exemple: rue paul,25 / chemin de montignies, 23b'))
                ]
            ])

            ->add('city',EntityType::class,[
                'class' => City::class,
                'choice_label' => 'localite',
                'placeholder' => 'choisisez votre ville',
                'autocomplete' => true,
                'label'=>false,
                'required' => false,
                'attr' => [
                    'autocomplete' => 'off'
                ]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
