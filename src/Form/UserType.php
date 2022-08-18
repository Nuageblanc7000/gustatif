<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
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
            ->add('email',EmailType::class,[
            ])
            ->add('pseudo',TextType::class,[
                'attr' =>[
                    'placeholder' => 'pseudo'
                ]
            ])
            ->add('isResto',CheckboxType::class,[
                'label' => $this->translator->trans('Etes vous restaurateur?'),
                'attr' => [
                    'class'=> 'form-boolean'
                ]
            ])
            ->add('password',RepeatedType::class,
            [
                'type' => PasswordType::class,
                'invalid_message' => $this->translator->trans('les mots de passes ne sont pas identitque'),
                'options' => ['attr' => ['class' => 'password-confirm']],
                'required' => true,
                "first_options" =>[ "label" => $this->translator->trans("Mot de passe") , 'attr'=>["placeholder" => '#487Robs2'] ],
                'second_options' => ['label' => $this->translator->trans('Confirmer le mot de passe') , 'attr'=>["placeholder" => '#487Robs2'] ],
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
