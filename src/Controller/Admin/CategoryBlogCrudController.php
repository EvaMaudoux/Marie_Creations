<?php

namespace App\Controller\Admin;

use App\Entity\CategoryBlog;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryBlogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CategoryBlog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural("Mes catégories de blog")
            ->setEntityLabelInSingular("catégorie")
            ->setPageTitle("index","gestion de mes catégories de blog")
            ->setPaginatorPageSize(20)
            ->setSearchFields(['title']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom de la catégorie de blog'),
        ];
    }
}
