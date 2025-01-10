<?php

namespace App\Form;

use App\Entity\TEncounterEnc;
use App\Entity\TChampionshipChp;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TeamChampionshipType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('championship', EntityType::class, [
                'class' => TChampionshipChp::class,
                'choice_label' => function(TChampionshipChp $championship) {
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
            'data_class' => TEncounterEnc::class,
        ]);
    }
}
