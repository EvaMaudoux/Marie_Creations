<?php

namespace App\Controller;

use App\Repository\WorkshopRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WorkshopController extends AbstractController
{
    #[Route('/ateliers', name: 'app_workshop')]
    public function workshop (WorkshopRepository $workshopRepo): Response
    {
        $workshops = $workshopRepo -> findBy (
            [],
            ['name' => 'ASC']
        );

        return $this->render('workshop/workshop.html.twig', [
            'workshops' => $workshops,
        ]);
    }
}
