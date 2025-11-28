<?php

namespace App\Controller\Admin;

use App\Repository\ArtistRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Repository\WorkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminArtistController extends AbstractController
{
    #[Route('/admin/artists', name: 'app_artists_panel')]
    #[isGranted('ROLE_ADMIN')]
    public function artistPanel(ArtistRepository $artistRepository): Response
    {
        $artists = $artistRepository->findAll();
        return $this->render('pages/admin/artist_panel.html.twig', [
            'artists' => $artists,
        ]);
    }
}
