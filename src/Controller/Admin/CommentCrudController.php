<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural("Les commentaires d'articles de blog")
            ->setEntityLabelInSingular("commentaire")
            ->setPageTitle("index","gestion des commentaires des utilisateurs")
            ->setPaginatorPageSize(20)
            ->setSearchFields(['ArticleId']);
    }


    public function configureActions(Actions $actions): Actions
    {
        return $actions

            ->disable(Action::NEW, Action::DELETE)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre')
                ->hideOnForm(),
            TextEditorField::new('content', 'Contenu')
                ->hideOnForm(),
            BooleanField::new('is_published', 'Publication'),
            AssociationField::new('user_id', 'Auteur ')
                ->hideOnForm(),
            AssociationField::new('article_id', 'Article commenté')
                ->hideOnForm(),
        ];
    }

}
