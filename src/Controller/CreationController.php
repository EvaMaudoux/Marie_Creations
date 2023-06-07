<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreationController extends AbstractController
{
    #[Route('/creations', name: 'app_creations')]
    public function index(): Response
    {
        return $this->render('creation/creations.html.twig');
    }


    #[Route('/creation', name: 'app_creation')]
    public function detail(): Response
    {
        return $this->render('creation/details_creation.html.twig');
    }
}

