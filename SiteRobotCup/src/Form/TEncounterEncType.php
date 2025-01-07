<?php

namespace App\Form;

use App\Entity\TChampionshipChp;
use App\Entity\TEncounterEnc;
use App\Entity\TTeamTem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TEncounterEncType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('scoreBlue')
            ->add('scoreGreen')
            ->add('state')
            ->add('teamBlue', EntityType::class, [
                'class' => TTeamTem::class,
                'choice_label' => 'id',
            ])
            ->add('teamGreen', EntityType::class, [
                'class' => TTeamTem::class,
                'choice_label' => 'id',
            ])
            ->add('championship', EntityType::class, [
                'class' => TChampionshipChp::class,
                'choice_label' => 'id',
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
