<?php

namespace App\Controller\Admin;

use App\Entity\Artist;
use App\Repository\ArtistRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Repository\WorkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminPanelController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_panel')]
    #[isGranted('ROLE_ADMIN')]
    public function adminPanel(ArtistRepository $artistRepository, WorkRepository $workRepository, CommentRepository $commentRepository, UserRepository $userRepository): Response
    {
        $stats = [
            'artists' => $artistRepository->count(),
            'works' => $workRepository->count(),
            'comments' => $commentRepository->count(),
            'comments_pending' => $commentRepository->count(['isPublic' => false]),
            'users' => $userRepository->count()
        ];
        return $this->render('pages/admin/admin_panel.html.twig', [
            'stats' => $stats
        ]);
    }
}
