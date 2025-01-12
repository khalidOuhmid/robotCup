<?php

namespace App\Form;

use App\Entity\Encounter;
use App\Entity\Championship;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TeamChampionshipForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('championship', EntityType::class, [
                'class' => Championship::class,
                'choice_label' => function(Championship $championship) {
                    return $championship->getCompetition()->getCmpName();
                },
                'label' => false,
                'required' => true,
                'placeholder' => 'Sélectionner un championnat'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Encounter::class,
        ]);
    }
}
