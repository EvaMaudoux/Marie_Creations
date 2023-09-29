<?php

namespace App\Controller;

use App\Entity\ArticleBlog;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentFormType;
use App\Repository\ArticleBlogRepository;
use App\Repository\CategoryArtRepository;
use App\Repository\CategoryBlogRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function blog(ArticleBlogRepository $blogRepo, CategoryBlogRepository $categoryRepo): Response
    {
        $categories = $categoryRepo -> findBy (
            [],
            ['name' => 'ASC']
        );


        $articles = $blogRepo->findBy(
            ['is_published' => true],
            ['created_at' => 'DESC'],
        );

        return $this->render('blog/blog.html.twig', [
            'articles' => $articles,
            'categories' => $categories,
        ]);
    }

    #[Route('/article/{slug}', name: 'app_article')]
    public function article(Request $request, ArticleBlog $article, CommentRepository $commentRepo, ArticleBlogRepository $articleBlogRepo, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        $comments = $commentRepo->findBy(
            ['article_id' => $article, 'is_published' => true],
            ['created_at' => 'DESC']
        );

        $lastArticles = $articleBlogRepo->findBy(
            ['is_published' => true],
            ['created_at' => 'DESC'],
            10,
        );

        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($user instanceof User) {
        if ($form->isSubmitted() && $form->isValid()) {
            $comment ->setArticleId($article)
                    ->setUserId($user)
                    ->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($comment);
            $manager->flush();

            // Redirigez l'utilisateur vers la page de l'article ou la page de commentaires après la création du commentaire
            return $this->redirectToRoute('app_article',
                ['slug' => $article->getSlug()]
            );
        }
        }
        // Affichez le formulaire avec les erreurs s'il y en a
        return $this->render('blog/blog-article.html.twig', [
            'article' => $article,
            'lastArticles' => $lastArticles,
            'form' => $form,
            'comments' => $comments,
        ]);

    }
}
