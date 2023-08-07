<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions

            ->disable(Action::NEW, Action::DELETE)
            ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural("Utilisateurs")
            ->setEntityLabelInSingular("utilisateur")
            ->setPageTitle("index","gestion des utilisateurs")
            ->setPaginatorPageSize(20)
            ->setDefaultSort(['lastName' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ImageField::new('imageName', 'image de profil')
                ->setBasePath('img/users')
                ->setUploadDir('public/img/users')
                ->hideOnForm(),
            TextField::new('firstName', 'Prénom')
                ->setFormTypeOption('disabled', 'disabled'),
            TextField::new('lastName', 'Nom')
                ->setFormTypeOption('disabled', 'disabled'),
            EmailField::new('email', 'Email')
                ->setFormTypeOption('disabled', 'disabled'),
            DateTimeField::new('createdAt', 'Date d\'inscription')
                ->hideOnForm(),
           /* ArrayField::new('likes', 'Tableaux aimés par l\'utilisateur')
                ->hideOnForm(),
            */
            ArrayField::new('reservations', 'Réservations d\'ateliers'),
            ArrayField::new('roles', 'Rôle'),

        ];
    }
}
