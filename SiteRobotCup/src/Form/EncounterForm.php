<?php

namespace App\Form;

use App\Entity\Encounter;
use App\Entity\Field;
use App\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EncounterForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('teamBlue', EntityType::class, [
                'class' => Team::class,
                'choice_label' => 'name',
                'label' => 'Équipe bleue',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('teamGreen', EntityType::class, [
                'class' => Team::class,
                'choice_label' => 'name',
                'label' => 'Équipe verte',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('field', EntityType::class, [
                'class' => Field::class,
                'choice_label' => 'name',
                'label' => 'Terrain',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('dateBegin', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de début',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('dateEnd', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('state', ChoiceType::class, [
                'choices' => array_combine(Encounter::STATES, Encounter::STATES),
                'label' => 'État',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Encounter::class,
        ]);
    }
}
