<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class CommentController extends AbstractController
{
    #[Route('comment/edit/{id}', name: 'app_comment_edit')]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $em): Response
    {
        if ($comment->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous n'êtes pas l'auteur de ce commentaire.");
        }
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setIsPublic(false);
            $comment->setPublishedAt(new \DateTimeImmutable());
            $em->flush();
            $this->addFlash('success', 'Votre commentaire a été modifié et est en attente de validation.');
            return $this->redirectToRoute('app_work', ['slug' => $comment->getWork()->getSlug()]);
        }

        return $this->render('pages/edit_comment.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('comment/delete/{id}', name: 'app_comment_delete')]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $em): Response
    {
        if ($comment->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
            $em->remove($comment);
            $em->flush();
            $this->addFlash('success', 'Votre commentaire a été supprimé.');
            $redirect = $request->query->get('redirect');

            if ($redirect === 'profile') {
                return $this->redirectToRoute('app_comments'); // Ta route "Mes commentaires"
            }
        return $this->redirectToRoute('app_work', ['slug' => $comment->getWork()->getSlug()]);
    }
}
