<?php
// src/Form/CompetitionType.php
namespace App\Form;

use App\Entity\Competition;
use App\Entity\Tournament;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompetitionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cmpName', TextType::class, [
                'label' => 'Nom de la compétition',
                'required' => true,
            ])
            ->add('cmpDesc', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('cmpAddress', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
            ])
            ->add('cmpDateBegin', DateTimeType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('cmpDateEnd', DateTimeType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('includeTournament', CheckboxType::class, [
                'label' => 'Inclure un tournoi',
                'required' => false,
                'mapped' => false,
            ])
            ->add('tournamentType', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'choices' => [
                    'Normal' => 'NORMAL',
                    'Système Suisse' => 'SUISSE',
                    'Système Hollandais' => 'HOLLANDAIS',
                ],
                'label' => 'Type de tournoi'
            ])
            ->add('includeThirdPlace', CheckboxType::class, [
                'label' => 'Inclure une petite finale',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'disabled' => true,
                    'class' => 'third-place-checkbox'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Competition::class,
        ]);
    }
}