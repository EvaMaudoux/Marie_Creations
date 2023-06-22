<?php

namespace App\Controller;

use App\Repository\ArticleBlogRepository;
use App\Repository\CreationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /** page d'accueil
     * @param CreationRepository $creationRepo
     * @param ArticleBlogRepository $articleRepo
     * @return Response
     */
    #[Route('/', name: 'app_home')]
    public function home(CreationRepository $creationRepo, ArticleBlogRepository $articleRepo): Response
    {
        $creations = $creationRepo -> findBy(
            [],
            ['date' => 'DESC'],
            3
        );
        $articles = $articleRepo -> findBy(
            [],
            ['created_at' => 'DESC'],
            2
        );
        return $this->render('home/home.html.twig',
        [
            'creations' => $creations,
            'articles' => $articles,
        ]);
    }
}
