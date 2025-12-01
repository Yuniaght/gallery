<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminUserController extends AbstractController
{
    #[Route('/admin/users', name: 'app_users_panel')]
    #[isGranted('ROLE_ADMIN')]
    public function UserPanel(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('pages/admin/user_panel.html.twig', [
            'users' => $users,
        ]);
    }
}
