<?php

namespace App\Form;

use App\Entity\Plat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class PlatType extends AbstractType
{
    public function __construct(public TranslatorInterface $translator)
    {
        
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                ->add('images',CollectionType::class,[
                    'entry_type' => PlatUploadType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'required' => false,
                    'mapped' => false,
                    'label' => false,
                 'attr' => [
                    'class' => 'container-grid-all'  ,'always_empty' =>false 
                 ],
                 'constraints' => [new Count(max:4, maxMessage:'vous avez dépassé le nombre d`\image autorisées')], 
                ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plat::class,
        ]);
    }
}
