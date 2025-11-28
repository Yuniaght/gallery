<?php

namespace App\Controller\User;

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
}
