<?php

namespace App\Controller\Admin;

use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminCommentController extends AbstractController
{
    #[Route('/admin/comments', name: 'app_comments_panel')]
    #[isGranted('ROLE_MODERATOR')]
    public function commentPanel(CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findAll();
        return $this->render('pages/admin/comment_panel.html.twig', [
            'comments' => $comments,
        ]);
    }

    #[Route('/admin/comments_in_pending', name: 'app_comments_pending_panel')]
    #[isGranted('ROLE_MODERATOR')]
    public function commentInPendingPanel(CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findBy(
            ['isPublic' => false],
        );
        return $this->render('pages/admin/comment_panel.html.twig', [
            'comments' => $comments,
        ]);
    }
}
