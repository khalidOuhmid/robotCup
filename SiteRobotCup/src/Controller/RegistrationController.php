<?php


namespace App\Controller;


use App\Entity\TUserUsr;
use App\Form\RegistrationFormType;
use App\Security\LoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


class RegistrationController extends AbstractController
{
   #[Route('/register', name: 'register')]  // Changed from 'app_register' to 'register'
   public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
   {
       // Redirect if user is already logged in
       if ($this->getUser()) {
           return $this->redirectToRoute('app_page_tableau_scores');
       }

       $user = new TUserUsr();
       $form = $this->createForm(RegistrationFormType::class, $user);
       $form->handleRequest($request);


       if ($form->isSubmitted() && $form->isValid()) {
           /** @var string $plainPassword */
           $plainPassword = $form->get('plainPassword')->getData();

           $user->setType('USER'); // Set default type for new users
           // encode the plain password
           $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));


           $entityManager->persist($user);
           $entityManager->flush();

           // Simplify by just redirecting to login
           return $this->redirectToRoute('app_login');
       }


       return $this->render('registration/register.html.twig', [
           'registrationForm' => $form,
       ]);
   }
}
