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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class CompetitionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cmpName', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('cmpAddress', TextType::class, [
                'label' => 'Lieu',
                'required' => false
            ])
            ->add('cmpDesc', TextareaType::class, [
                'label' => 'Description',
                'required' => false
            ])
            ->add('cmpDateBegin', DateTimeType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text'
            ])
            ->add('cmpDateEnd', DateTimeType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text'
            ])
            ->add('includeTournament', CheckboxType::class, [
                'label' => 'Inclure un tournoi',
                'mapped' => false,
                'required' => false
            ])
            ->add('cmpRoundSystem', ChoiceType::class, [
                'label' => 'Type de tournoi',
                'choices' => [
                    'Ronde Suisse' => 'SUISSE',
                    'Ronde Hollandaise' => 'HOLLANDAIS',
                    'Système Monrad' => 'MONRAD'
                ],
                'placeholder' => 'Choisir un type de tournoi',
                'required' => false,
                'mapped' => true,
            ])
            ->add('cmpRounds', IntegerType::class, [
                'label' => 'Nombre de rondes',
                'required' => false,
                'mapped' => true,
                'attr' => [
                    'min' => 1,
                    'max' => 16
                ],
                'constraints' => [
                    new Range(['min' => 1, 'max' => 16])
                ]
            ])
            ->add('includeThirdPlace', CheckboxType::class, [
                'label' => 'Match pour la 3ème place',
                'mapped' => false,
                'required' => false
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