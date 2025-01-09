<?php

namespace App\Form;

use App\Entity\TUserUsr;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TUserUsrType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class, [
                'empty_data' => '',
                'required' => true,
                'attr' => ['autocomplete' => 'new-password'],
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Utilisateur' => 'USER',
                    'Administrateur' => 'ADMIN'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TUserUsr::class,
        ]);
    }
}
