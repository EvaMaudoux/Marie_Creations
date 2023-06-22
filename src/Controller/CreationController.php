<?php

namespace App\Controller;

use App\Entity\Creation;
use App\Repository\CategoryArtRepository;
use App\Repository\CreationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreationController extends AbstractController
{
    #[Route('/creations', name: 'app_creations')]
    public function creations(CreationRepository $creationRepo, CategoryArtRepository $categorieRepo): Response
    {
        $categories = $categorieRepo -> findBy (
            [],
            ['name' => 'ASC']
        );

        $creations = $creationRepo -> findBy (
            [],
            ['title' => 'ASC']
        );

        return $this->render('creation/creations.html.twig', [
            'categories' => $categories,
            'creations' => $creations,
        ]);
    }


    /**
     * @param Creation $creation
     * @return Response
     */
    #[Route('/creation/{slug}', name: 'app_creation')]
    public function creation(Creation $creation): Response
    {
        return $this->render('creation/creation.html.twig', [
            'creation' => $creation
        ]);
    }
}

