<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AtelierController extends AbstractController
{
    #[Route('/ateliers', name: 'app_ateliers')]
    public function index(): Response
    {
        return $this->render('atelier/ateliers.html.twig');
    }
}
