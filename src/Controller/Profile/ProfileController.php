<?php

namespace App\Controller\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile', name: 'profile_')]
class ProfileController extends AbstractController
{
    #[Route('/compte', name: 'account')]
    public function myAccount(): Response
    {
        // recuperer id de user
        return $this->render('profile/index.html.twig');
    }
}