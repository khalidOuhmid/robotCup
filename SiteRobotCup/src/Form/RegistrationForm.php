<?php


namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class RegistrationForm extends AbstractType
{
   public function buildForm(FormBuilderInterface $builder, array $options): void
   {
       $builder
           ->add('email')
           ->add('plainPassword', PasswordType::class, [
               'mapped' => false,
               'attr' => ['autocomplete' => 'new-password'],
               'constraints' => [
                   new NotBlank([
                       'message' => 'Le mot de passe est requis',
                   ]),
                   new Length([
                       'min' => 6,
                       'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                       'max' => 4096,
                   ]),
               ],
           ])
           ->add('_token', HiddenType::class, [
               'mapped' => false,
               'data' => 'register'
           ])
       ;
   }


   public function configureOptions(OptionsResolver $resolver): void
   {
       $resolver->setDefaults([
           'data_class' => User::class,
       ]);
   }
}
