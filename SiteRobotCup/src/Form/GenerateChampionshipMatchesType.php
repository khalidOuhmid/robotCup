<?php
// src/Form/GenerateChampionshipMatchesType.php
namespace App\Form;

use App\Entity\Championship;
use App\Entity\Field;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GenerateChampionshipMatchesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('championship', EntityType::class, [
                'class' => Championship::class,
                'choice_label' => function($championship) {
                    return $championship->getCompetition()->getCmpName() . ' - Championship #' . $championship->getId();
                },
                'required' => true,
            ])
            ->add('field', EntityType::class, [
                'class' => Field::class,
                'choice_label' => 'name',
                'required' => true,
            ])
            ->add('timeSlots', CollectionType::class, [
                'entry_type' => TimeSlotType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
