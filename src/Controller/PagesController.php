<?php

namespace App\Controller;

use App\Repository\WorkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PagesController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(WorkRepository $repository): Response
    {
        $latestWork = $repository->findBy(
            [],
            ["addedAt" => "DESC"],
            3
        );
        return $this->render('pages/home.html.twig',
            [
                "latestWork" => $latestWork,
            ]);
    }

    #[Route('/gallery', name: 'app_gallery')]
    public function gallery(): Response
    {
        return $this->render('pages/gallery.html.twig', [
            'controller_name' => 'PagesController - Gallery',
        ]);
    }

    #[Route('/gallery/{slug}', name: 'app_galleryByCategory')]
    public function galleryByCategory(string $slug): Response
    {
        return $this->render('pages/gallery.html.twig', [
            'controller_name' => 'PagesController - Gallery - ' . $slug,
        ]);
    }

    #[Route('/gallery/view/{slug}', name: 'app_work')]
    public function works(string $slug): Response
    {
        return $this->render('pages/work.html.twig', [
            'controller_name' => 'PagesController - Work - ' . $slug,
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('pages/about.html.twig', [
            'controller_name' => 'PagesController - About',
        ]);
    }

    #[Route('/team', name: 'app_team')]
    public function team(): Response
    {
        return $this->render('pages/team.html.twig', [
            'controller_name' => 'PagesController - Team',
        ]);
    }
}
