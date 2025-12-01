<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[isGranted('ROLE_MODERATOR')]
final class AdminCommentController extends AbstractController
{
    #[Route('/admin/comments', name: 'app_comments_panel')]

    public function commentPanel(CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findAll();
        return $this->render('pages/admin/comment_panel.html.twig', [
            'comments' => $comments,
        ]);
    }

    #[Route('/admin/comments_in_pending', name: 'app_comments_pending_panel')]
    public function commentInPendingPanel(CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findBy(
            ['isPublic' => false],
        );
        return $this->render('pages/admin/comment_panel.html.twig', [
            'comments' => $comments,
        ]);
    }

    #[Route('/admin/validate_comment/{id}', name: 'app_validate_comment')]
    public function validateComment (Comment $comment, EntityManagerInterface $entityManager, Request $request): Response
    {
        $comment->setIsPublic(!$comment->isPublic());
        $entityManager->flush();
        $message = $comment->isPublic() ? "Le commentaire a été rendu publique" : "Le commentaire a été masqué";
        $this->addFlash('success', $message);
        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/admin/delete_comment/{id}', name: 'app_delete_comment')]
    public function deleteComment (Comment $comment, EntityManagerInterface $entityManager, Request $request): Response
    {
        $entityManager->remove($comment);
        $entityManager->flush();
        $this->addFlash('success', 'Le commentaire a été supprimé');
        return $this->redirect($request->headers->get('referer'));
    }
}
