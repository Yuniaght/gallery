<?php

namespace App\Controller\Admin;

use App\Repository\WorkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminWorkController extends AbstractController
{
    #[Route('/admin/works', name: 'app_works_panel')]
    #[isGranted('ROLE_ADMIN')]
    public function workPanel(WorkRepository $workRepository): Response
    {
        $works = $workRepository->findAll();
        return $this->render('pages/admin/work_panel.html.twig', [
            'works' => $works,
        ]);
    }
}
