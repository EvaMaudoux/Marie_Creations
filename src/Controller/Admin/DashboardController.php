<?php

namespace App\Controller\Admin;

use App\Entity\ArticleBlog;
use App\Entity\Calendar;
use App\Entity\CategoryArt;
use App\Entity\CategoryBlog;
use App\Entity\Comment;
use App\Entity\Creation;
use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Workshop;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator) {
    }

    #[Route('/admin', name: 'app_admin')]
    public function admin(): Response
    {
        $url = $this->adminUrlGenerator
            ->setController(CreationCrudController::class)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Atelier & Créations de Marie - Administration')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        // Section users
        yield MenuItem::section('Utilisateurs inscrits', 'fa-solid fa-user');
        yield MenuItem::subMenu('Action', 'fas fa-bars')->setSubItems([
             MenuItem::linkToCrud('Voir les utilisateurs', 'fa-solid fa-eye', User::class),
        ]);

        // Section catégories artistiques
        yield MenuItem::section('Catégories artistiques', 'fa-solid fa-palette');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Voir mes catégories', 'fa-solid fa-eye', CategoryArt::class),
            MenuItem::linkToCrud('Ajouter une nouvelle catégorie', 'fa-solid fa-plus', CategoryArt::class)->setAction(Crud::PAGE_NEW),
        ]);

        // Section créations
        yield MenuItem::section('Créations', 'fa-solid fa-palette');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Voir mes créations', 'fa-solid fa-eye', Creation::class),
            MenuItem::linkToCrud('Ajouter une nouvelle création', 'fa-solid fa-plus', Creation::class)->setAction(Crud::PAGE_NEW),
        ]);

        // Section ateliers
        yield MenuItem::section('Ateliers', 'fa-solid fa-paintbrush');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Voir mes ateliers', 'fa-solid fa-eye', Workshop::class),
            MenuItem::linkToCrud('Ajouter un nouvel atelier', 'fa-solid fa-plus', Workshop::class)->setAction(Crud::PAGE_NEW),
        ]);

        // Section sessions d'ateliers
        yield MenuItem::section('Sessions d\'ateliers', 'fa-solid fa-calendar-days');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Voir mes ateliers prévus', 'fa-solid fa-eye', Calendar::class),
            MenuItem::linkToCrud('Programmer un nouvel atelier', 'fa-solid fa-plus', Calendar::class)->setAction(Crud::PAGE_NEW),
        ]);

        // Section réservations
        yield MenuItem::section('Inscriptions aux ateliers', 'fa-solid fa-user-plus');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Voir les inscriptions', 'fa-solid fa-eye', Reservation::class),
            MenuItem::linkToCrud('Inscrire un participant', 'fa-solid fa-plus', Reservation::class)->setAction(Crud::PAGE_NEW),
           ]);


        // Section catégories du blog
        yield MenuItem::section('Catégories du blog', 'fa-solid fa-newspaper');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Voir mes catégories', 'fa-solid fa-eye', CategoryBlog::class),
            MenuItem::linkToCrud('Ajouter une nouvelle catégorie', 'fa-solid fa-plus', CategoryBlog::class)->setAction(Crud::PAGE_NEW),
        ]);

        // Section articles de blog
        yield MenuItem::section('Articles de blog', 'fa-solid fa-newspaper');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Voir mes articles', 'fa-solid fa-eye', ArticleBlog::class),
            MenuItem::linkToCrud('Ajouter un nouvel article', 'fa-solid fa-plus', ArticleBlog::class)->setAction(Crud::PAGE_NEW),
        ]);

        // Section commentaires des articles de blog
        yield MenuItem::section('Commentaires du blog', 'fa-solid fa-comment');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Voir les commentaires utilisateurs', 'fa-solid fa-eye', Comment::class),
        ]);

        // Retour vers la partie publique
        yield MenuItem::linktoRoute('Retour vers la partie publique du site', 'fas fa-home', 'app_home');
    }

}
