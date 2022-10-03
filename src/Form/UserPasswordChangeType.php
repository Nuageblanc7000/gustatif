<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class UserPasswordChangeType extends AbstractType
{
    public function __construct(public TranslatorInterface $translator)
    { 
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password',RepeatedType::class,
            [
                'type' => PasswordType::class,
                'mapped' => false,
                'attr'=> ['always_empty' =>false],
                'invalid_message' => $this->translator->trans('les mots de passes ne sont pas identitque'),
                'options' => ['attr' => ['class' => 'password-confirm','always_empty' =>false]],
                'required' => true,
                "first_options" =>[ "label" => $this->translator->trans("Nouveau mot de passe") , 'attr'=>["placeholder" => '#487Robs2'] , 'always_empty' =>false , ],
                'second_options' => ['label' => $this->translator->trans('Confirmer le mot de passe') , 'attr'=>["placeholder" => '#487Robs2'] , 'always_empty' =>false,  ],
                'constraints' => [
                    new NotNull(message: $this->translator->trans('veuillez compléter les champs')),
                    new Length(min:5,minMessage: $this->translator->trans('minimum 5 caractères'))
                ]
                
            ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
