<?php
// src/Form/TimeSlotType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class TimeSlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startTime', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('endTime', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('matchCount', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 10,
                ],
                'required' => true,
            ])
        ;
    }
}