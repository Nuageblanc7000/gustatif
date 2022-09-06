<?php

namespace App\Form;

use App\Entity\Comment;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Contracts\Translation\TranslatorInterface;

class CommentType extends AbstractType

{
    public function __construct(public TranslatorInterface $translator)
    {
        
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description',TextareaType::class,[
                'constraints'=> [
                    new NotNull(message:$this->translator->trans('Veuillez indiquer votre avis')),
                    new Length(min:4,minMessage:$this->translator->trans('Le commentaire doit faire minimum 4 caractères'),max:500 , maxMessage:$this->translator->trans('Le commentaire doit faire maximum 500 caractères'))
                ],
                'empty_data' => false,
                'required' => true
            ])
            ->add('rating',HiddenType::class,[
                 'constraints' => [
                    new Range(min:0,max:5,notInRangeMessage:$this->translator->trans('la valeur doit être entre 0 et 5'))
                 ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
