<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/user/{id}', name: 'app_user', methods:['GET'])]
    public function index(User $user): Response
    {  
        
        return $this->render('user/index.html.twig', [
            'user' => $user,
            
        ]);
    }
}
