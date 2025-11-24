<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArtistController extends AbstractController
{
    #[Route('/artists', name: 'app_artists')]
    public function artists(): Response
    {
        return $this->render('pages/artists.html.twig',
            [
                'controller_name' => 'ArtistController',
            ]);
    }

    #[Route('/artist/{slug:slug}', name: 'app_artist')]
    public function artist(string $slug): Response
    {
        return $this->render('pages/artist.html.twig', [
            'controller_name' => 'ArtistController',
        ]);
    }
}

