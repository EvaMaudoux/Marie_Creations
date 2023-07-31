<?php

namespace App\Controller\Admin;

use App\Entity\CategoryArt;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryArtCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CategoryArt::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural("Les catégories artistiques")
            ->setEntityLabelInSingular("catégorie artistique")
            ->setPageTitle("index","gestion des catégories artistiques")
            ->setPaginatorPageSize(20)
            ->setSearchFields(['title']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom de la catégorie artistique'),
        ];
    }
}
