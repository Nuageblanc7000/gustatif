<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


class UserType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,
            [      'required' =>false,
                'attr'=> ['autocomplete' => 'disabled','list'=>"autocompleteOff", 'autofocus' => false , 'placeholder' => $this->translator->trans('Votre email')]]
            
            )

            ->add('pseudo',TextType::class,[
                'required' =>false,
                'attr' =>[
                    'placeholder' => $this->translator->trans('pseudo')
                    ]
                    ])
                    ->add('city',EntityType::class,[
                        'class' => City::class,
                        'choice_label' => 'localite',
                        'label' => $this->translator->trans('ville'),
                        'placeholder' => $this->translator->trans('Ville'),
                        'required'=> false,
                        'autocomplete' => true,
                        'attr' => [
                            'autocomplete' => 'disabled'
                        ]
                    ])
                    
            ->add('isResto',CheckboxType::class,[
                'label' => $this->translator->trans('Êtes vous restaurateur?'),
                'required' => false,
                'attr' => [
                    'class'=> 'form-boolean'
                ]
            ])
            ->add('password',RepeatedType::class,
            
            
            [
                'required' =>false,
                'type' => PasswordType::class,
                'attr'=> ['always_empty' =>true],
                'invalid_message' => $this->translator->trans('les mots de passes ne sont pas identitque'),
                'options' => ['attr' => ['class' => 'password-confirm']],
                'required' => true,
                "first_options" =>[ "label" => $this->translator->trans("Mot de passe") , 'attr'=>["placeholder" => '#487Robs2','autocomplete' => 'disabled']],
                'second_options' => ['label' => $this->translator->trans('Confirmer le mot de passe') , 'attr'=>["placeholder" => '#487Robs2']],
            ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
