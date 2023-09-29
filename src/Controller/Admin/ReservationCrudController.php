<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ReservationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reservation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural("Les inscriptions d'utilisateurs aux ateliers")
            ->setEntityLabelInSingular("inscription")
            ->setPageTitle("index","gestion des inscriptions aux ateliers")
            ->setPaginatorPageSize(20)
            ->setSearchFields(['workshop'])
            ->setDefaultSort(['workshop' => 'DESC']);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('user_id', 'Utilisateur'),
            AssociationField::new('workshop', 'Atelier'),
            DateTimeField::new('createdAt ', 'Date de réservation')
            ->hideOnForm(),
            TextField::new('status', 'Statut de la réservation'),
        ];
    }

}
