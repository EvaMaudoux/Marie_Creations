<?php

namespace App\Controller\Admin;

use App\Entity\CategoryArt;
use App\Entity\Creation;
use App\Entity\User;
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
            yield MenuItem::section('Les utilisateurs inscrits', 'fa-solid fa-user');
            yield MenuItem::subMenu('Voir', 'fas fa-bars')->setSubItems([
                MenuItem::linkToCrud('Tous les utilisateurs inscrits', 'fa-solid fa-eye', User::class),
            ]);


        // Section créations
        yield MenuItem::section('Mes créations', 'fa-solid fa-palette');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Voir mes créations', 'fa-solid fa-eye', Creation::class),
            MenuItem::linkToCrud('Ajouter une nouvelle création', 'fa-solid fa-plus', Creation::class)->setAction(Crud::PAGE_NEW),
        ]);

        // Section catégories de créations
        yield MenuItem::section('Catégories créatives', 'fa-solid fa-palette');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Liste des catégories', 'fa-solid fa-eye', CategoryArt::class),
            MenuItem::linkToCrud('Ajouter une nouvelle catégorie', 'fa-solid fa-plus', CategoryArt::class)->setAction(Crud::PAGE_NEW),
        ]);

        // Retour vers la partie publique
        yield MenuItem::linktoRoute('Retour vers la partie publique du site', 'fas fa-home', 'app_home');
    }

}
