<?php

namespace App\Form;


use App\Entity\ResetPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ResetPasswordType extends AbstractType
{
    public function __construct(public TranslatorInterface $translator )
    {
        
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mail',EmailType::class,[
                'constraints' => [
                    new NotNull(),
                    new Email(message: $this->translator->trans('veuillez insérer un email valide'))
                ],
                'attr' => ['placeholder' => $this->translator->trans('Entrer votre email')]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ResetPassword::class
        ]);
    }
}
