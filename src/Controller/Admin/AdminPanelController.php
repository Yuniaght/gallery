<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminPanelController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_admin_panel')]
    #[isGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        return $this->render('pages/admin/index.html.twig', [
            'controller_name' => 'Admin/AdminPanelController',
        ]);
    }
}
