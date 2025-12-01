<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserMailType;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[isGranted('ROLE_ADMIN')]
final class AdminUserController extends AbstractController
{
    #[Route('/admin/users', name: 'app_users_panel')]
    public function UserPanel(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('pages/admin/user_panel.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/delete_user/{id}', name: 'app_delete_user')]
    public function deleteUser(User $user,EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('success', 'L\'utilisateur a été supprimé');
        return $this->redirectToRoute('app_users_panel');
    }

    #[Route('/admin/ban_user/{id}', name: 'app_ban_user')]
    public function banUser(User $user,EntityManagerInterface $entityManager, CommentRepository $commentRepository): Response
    {
        $user->setIsActive(!$user->isActive());
        $commentRepository->hideAllCommentsByUser($user);
        $entityManager->flush();
        $message = $user->isActive() ? "L'utilisateur a été réactivé" : "l'utilisateur a été désactivé";
        $this->addFlash('success', $message);
        return $this->redirectToRoute('app_users_panel');
    }

    #[Route('/admin/edit-user-email/{id}', name: 'admin_user_edit_email')]
    public function editEmail(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserMailType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'L\'adresse email a été modifiée avec succès.');
            return $this->redirectToRoute('app_users_panel');
        }

        return $this->render('pages/admin/usermail_form.html.twig', [
            'changeMailForm' => $form->createView(),
        ]);
    }
}
