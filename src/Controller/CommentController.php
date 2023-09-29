<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
   // #[Route('/article/comment', name: 'app_article_comment')] {}
//    public function comment(Request $request, CommentRepository $commentRepo, EntityManagerInterface $manager): Response
//    {
//
//        $comments = $commentRepo -> findBy (
//            [],
//            ['created_at' => 'DESC']
//        );
//
//        $comment = new Comment();
//        $form = $this->createForm(CommentFormType::class, $comment);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            // Récupérez l'utilisateur actuellement authentifié (s'il existe)
//            $user = $this->getUser();
//
//            if ($user) {
//                $comment->setUserId($user);
//            }
//
//            // Vous pouvez également définir d'autres propriétés de Comment ici, par exemple la date de création
//
//            $manager->persist($comment);
//            $manager->flush();
//
//            // Redirigez l'utilisateur vers la page de l'article ou la page de commentaires après la création du commentaire
//            return $this->redirectToRoute('app_article');
//        }
//
//        // Affichez le formulaire avec les erreurs s'il y en a
//        return $this->render('blog/blog-article.html.twig', [
//            'form' => $form->createView(),
//            'comments' => $comments,
//        ]);
//    }
}
