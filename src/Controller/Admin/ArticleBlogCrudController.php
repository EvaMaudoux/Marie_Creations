<?php

namespace App\Controller\Admin;

use App\Entity\ArticleBlog;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ArticleBlogCrudController extends AbstractCrudController
{

    public const CREATION_BASE_PATH = 'img/allCreations';
    public const CREATION_UPLOAD_DIR = 'public/img/allCreations';


    public static function getEntityFqcn(): string
    {
        return ArticleBlog::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural("Mes articles de blog")
            ->setEntityLabelInSingular("article de blog")
            ->setPageTitle("index","gestion de mes articles")
            ->setPaginatorPageSize(20)
            ->setSearchFields(['title'])
            ->setDefaultSort(['created_at' => 'DESC']);
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('categoryArticle', 'catégorie d\'article'),
            TextField::new('title'),
            TextEditorField::new('content'),
            ImageField::new('imageName', 'Image')
                ->setBasePath(self::CREATION_BASE_PATH)
                ->setUploadDir(self::CREATION_UPLOAD_DIR)
                ->setRequired(false),
            DateTimeField::new('createdAt ', 'Date de réservation')
                ->hideOnForm()
                ->setCustomOption('default', new \DateTime()),
            SlugField::new('slug', 'slug')
                ->setTargetFieldName('title')
                ->hideOnIndex(),
            BooleanField::new('isPublished', 'publication de l\'article'),
            CollectionField::new('comments', 'Commentaires utilisateurs')
                ->hideOnForm(),

        ];
    }

    /** Méthode du CrudAbstractController pour persister la date de création + le slug
     * @param EntityManagerInterface $em
     * @param $entityInstance
     * @return void
     */
    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        if(!$entityInstance instanceof ArticleBlog) return;
        $entityInstance->setCreatedAt(new \DateTimeImmutable());
        parent::persistEntity($em, $entityInstance);
    }

}
