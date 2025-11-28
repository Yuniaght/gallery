<?php

namespace App\Controller\User;

use App\Form\EditUserInfoType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Form\ChangePasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserChangeInfoController extends AbstractController
{
    #[Route('/user/editInfo', name: 'app_change_info')]
    #[IsGranted('ROLE_USER')]
    public function editInfo(Request $request, EntityManagerInterface $entityManager) : Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditUserInfoType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setEditedAt(new \DateTimeImmutable());
            $entityManager->flush();
            $this->addFlash('success', 'Vos informations ont été mises à jour.');
            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('pages/user/change_info.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/changePassword', name: 'app_change_password')]
    #[IsGranted('ROLE_USER')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager) : Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $newPassword
            );
            $user->setPassword($hashedPassword);
            $user->setEditedAt(new \DateTimeImmutable());
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Votre mot de passe a été modifié avec succès.');
            return $this->redirectToRoute('app_user_profile');
        }
        return $this->render('pages/user/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/deleteAccount', name: 'app_delete_account', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function deleteAccount(Request $request, EntityManagerInterface $entityManager) : Response
    {
        $user = $this->getUser();
        if ($this->isCsrfTokenValid('delete_account', $request->request->get('_token'))) {
            $request->getSession()->invalidate();
            $this->container->get('security.token_storage')->setToken(null);
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Votre compte a été supprimé avec succès.');
            return $this->redirectToRoute('app_home');
        }
        $this->addFlash('danger', 'Erreur de sécurité lors de la suppression.');
        return $this->redirectToRoute('app_user_profile');
    }
}
