<?php

namespace App\Controller\Admin;

use App\Repository\TechnicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminTechnicController extends AbstractController
{
    #[Route('/admin/technic', name: 'app_technic_panel')]
    #[isGranted('ROLE_ADMIN')]
    public function TechnicPanel(TechnicRepository $technicRepository): Response
    {
        $technics = $technicRepository->findAll();
        return $this->render('pages/admin/technic_panel.html.twig', [
            'Technics' => $technics,
        ]);
    }
}
