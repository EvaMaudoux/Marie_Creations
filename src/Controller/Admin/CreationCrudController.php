<?php

namespace App\Controller\Admin;

use App\Entity\Creation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CreationCrudController extends AbstractCrudController
{

    public const CREATION_BASE_PATH = 'img/allCreations';
    public const CREATION_UPLOAD_DIR = 'public/img/allCreations';


    public static function getEntityFqcn(): string
    {
        return Creation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural("Mes créations")
            ->setEntityLabelInSingular("création")
            ->setPageTitle("index","gestion de mes créations")
            ->setPaginatorPageSize(20)
            ->setSearchFields(['title'])
            ->setDefaultSort(['date' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ImageField::new('imageName', 'Image')
                ->setBasePath(self::CREATION_BASE_PATH)
                ->setUploadDir(self::CREATION_UPLOAD_DIR),
            TextField::new('title', 'Titre'),
            AssociationField::new('category', 'Catégorie artistique'),
            // BooleanField::new('isSold', 'Vendu'),
            // AssociationField::new('likes', 'Nombre de likes')->hideOnForm(),
            DateField::new('date', 'date de création'),
        ];
    }
}
