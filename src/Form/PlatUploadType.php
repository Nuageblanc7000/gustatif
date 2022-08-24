<?php

namespace App\Form;

use App\Entity\FalseImg;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PlatUploadType extends AbstractType
{
    public function __construct( public TranslatorInterface $translator )
    {
        
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('path',FileType::class,[
                'label' => ' ',
                'label_attr'=>['class'=>'label-input-file'],
                'required' => false,
                'constraints' => [
                    new File(mimeTypes:['image/*'],mimeTypesMessage: $this->translator->trans('veuillez mettre un fichier image (png,jpg,jpeg)'),maxSize:'100000k'),
                    new NotNull(message:$this->translator->trans('veuillez insérer une image')),
            ],    
            ]
            )

            ->add('alt',TextType::class,
            [
                'attr'=>  [],
                'constraints' => [
                    new NotNull(message:$this->translator->trans('Inscrire le nom de la spécialité')),
            ],    
            'label' =>  $this->translator->trans('Nom du plat'),
                'label_attr'=>['class'=>'label-input-text']
            
            ])

        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FalseImg::class,
        ]);
    }
}