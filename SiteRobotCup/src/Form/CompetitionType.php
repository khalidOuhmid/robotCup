<?php
// src/Form/CompetitionType.php
namespace App\Form;

use App\Entity\Competition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
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
            ->add('cmpRoundSystem', ChoiceType::class, [
                'label' => 'Système de rondes',
                'choices' => [
                    'Suisse' => 'SUISSE',
                    'Hollandais' => 'HOLLANDAIS',
                    'Normal' => 'NORMAL',
                ],
                'placeholder' => 'Choisir un type de championnat',
                'required' => true,
            ])
            ->add('cmpRounds', IntegerType::class, [
                'label' => 'Nombre de rondes',
                'required' => false,
                'attr' => [
                    'min' => 1,
                    'max' => 16
                ],
                'constraints' => [
                    new Range(['min' => 1, 'max' => 16])
                ]
            ])
            ->add('includeTournament', CheckboxType::class, [
                'label' => 'Inclure un tournoi',
                'mapped' => false,
                'required' => false
            ])
            ->add('includeThirdPlace', CheckboxType::class, [
                'label' => 'Inclure une petite finale',
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