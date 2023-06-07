<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/a-propos', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('page/about.html.twig');
    }
}
