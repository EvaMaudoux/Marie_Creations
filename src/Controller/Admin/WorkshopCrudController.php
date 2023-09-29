<?php

namespace App\Controller\Admin;

use App\Entity\Workshop;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class WorkshopCrudController extends AbstractCrudController
{
    public const CREATION_BASE_PATH = 'img/workshops';
    public const CREATION_UPLOAD_DIR = 'public/img/workshops';

    public static function getEntityFqcn(): string
    {
        return Workshop::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural("Mes ateliers")
            ->setEntityLabelInSingular("atelier")
            ->setPageTitle("index","gestion de mes ateliers")
            ->setPaginatorPageSize(10)
            ->setSearchFields(['name'])
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('category', 'catégorie artistique'),
            TextField::new('name', 'nom'),
            TextEditorField::new('description'),
            MoneyField::new('price', 'prix')->setCurrency('EUR')->setStoredAsCents(false),
            IntegerField::new('maxCapacity', 'nombre max de participants'),
            ImageField::new('imageName', 'Image')
                ->setBasePath(self::CREATION_BASE_PATH)
                ->setUploadDir(self::CREATION_UPLOAD_DIR),
        ];
    }

}
