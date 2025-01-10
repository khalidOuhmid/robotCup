<?php

namespace App\Form;

use App\Entity\TChampionshipChp;
use App\Entity\TCompetitionCmp;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TChampionshipChpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('competition', EntityType::class, [
                'class' => TCompetitionCmp::class,
                'choice_label' => 'cmpName',
                'label' => 'Competition',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TChampionshipChp::class,
        ]);
    }
}
