<?php

namespace App\Controller\Admin;

use App\Entity\Calendar;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CalendarCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Calendar::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural("Mes sessions d'ateliers")
            ->setEntityLabelInSingular("atelier")
            ->setPageTitle("index","gestion de mes ateliers")
            ->setPaginatorPageSize(10)
            ->setSearchFields(['title'])
            ->setDefaultSort(['start' => 'DESC']);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'nom'),
            TextEditorField::new('description'),
            AssociationField::new('workshop', 'catégorie d\'atelier'),
            DateTimeField::new('start', 'date et heure de début')->setFormTypeOption('html5', true),
            DateTimeField::new('end', 'date et heure de fin')->setFormTypeOption('html5', true),
            BooleanField::new('allDay', 'toute la journée'),
            ColorField::new('backgroundColor', 'agenda - couleur de fond'),
            ColorField::new('borderColor', 'agenda - couleur de bordure'),
            ColorField::new('textColor', 'agenda - couleur de texte'),
        ];
    }

}
