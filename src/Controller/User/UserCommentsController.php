<?php

namespace App\Controller\User;

use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserCommentsController extends AbstractController
{
    #[Route('/user/comments', name: 'app_comments')]
    #[IsGranted('ROLE_USER')]
    public function comments(CommentRepository $comment) : Response {
        $user = $this->getUser();
        $comments = $comment->findCommentsMadeByUser($user->getUsername());
        return $this->render('pages/user/comments.html.twig', [
            'comments' => $comments,
        ]);
    }
}
