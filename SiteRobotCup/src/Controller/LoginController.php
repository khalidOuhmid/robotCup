<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(): Response
    {
        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
        ]);
    }
        
}

function test($user, $password)
{
    echo 'ceci est un test ! username: '.$user.' password:'.$password;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    if (isset($_POST['username'] ) and $_POST['username'] != "" and isset($_POST['password'])){
        test($_POST['username'], $_POST['password']);
    }
}

