<?php

namespace App\Form;

use App\Entity\Schedule;
use App\Form\TimetableType;
use App\Validator\NotOnlyFieldEmpty;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('timetables',CollectionType::class,[
            'entry_type' => TimetableType::class,
            'label' => false,
            'constraints' => [new NotOnlyFieldEmpty()]

        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Schedule::class,
        ]);
    }
}
