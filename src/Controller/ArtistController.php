<?php

namespace App\Controller;

use App\Repository\ArtistRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArtistController extends AbstractController
{
    #[Route('/artists', name: 'app_artists')]
    public function artists(ArtistRepository $artist): Response
    {
        $artists = $artist->Findby([], ['lastName' => 'ASC', 'firstName' => 'ASC']);
        return $this->render('pages/artists.html.twig', [
            'artists' => $artists,
        ]);
    }

    #[Route('/artist/{slug:slug}', name: 'app_artist')]
    public function artist(ArtistRepository $artist,array $slug): Response
    {
        $artist = $artist->findOneBy(['slug' => $slug]);
        return $this->render('pages/artist.html.twig', [
            'artist' => $artist,
        ]);
    }
}

