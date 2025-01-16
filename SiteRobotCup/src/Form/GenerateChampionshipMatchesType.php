<?php
// src/Form/GenerateChampionshipMatchesType.php
namespace App\Form;

use App\Entity\Championship;
use App\Entity\Field;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
                'label' => 'Terrain'
            ])
            ->add('startTime', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => 'Début du créneau'
            ])
            ->add('endTime', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => 'Fin du créneau'
            ])
            ->add('matchDuration', IntegerType::class, [
                'required' => true,
                'label' => 'Durée d\'un match (en minutes)',
                'attr' => ['min' => 5],
            ])
            ->add('maxMatches', IntegerType::class, [
                'required' => true,
                'label' => 'Nombre maximum de matchs',
                'attr' => ['min' => 1],
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
