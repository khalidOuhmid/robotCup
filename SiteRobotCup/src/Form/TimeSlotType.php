<?php
// src/Form/TimeSlotType.php
namespace App\Form;

use App\Entity\TimeSlot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimeSlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateBegin', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => 'Date de début',
            ])
            ->add('dateEnd', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => 'Date de fin',
            ])
            ->add('matchCount', IntegerType::class, [
                'mapped' => false,  // Ne pas mapper à l'entité
                'required' => true,
                'attr' => [
                    'min' => 1
                ],
                'label' => 'Nombre de rencontres'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TimeSlot::class,
        ]);
    }
}