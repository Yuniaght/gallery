<?php

namespace App\Controller;

use App\Repository\WorkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexController extends AbstractController
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
}
