<?php

namespace App\Form;

use App\Entity\TTeamTem;
use App\Entity\TCompetitionCmp;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'équipe'
            ])
            ->add('structure', TextType::class, [
                'label' => 'Structure',
                'required' => false
            ])
            ->add('competition', EntityType::class, [
                'class' => TCompetitionCmp::class,
                'choice_label' => 'cmpName',
                'required' => true,
                'label' => 'Compétition'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TTeamTem::class,
        ]);
    }
}
