<?php

namespace App\Controller\Admin;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminCategoryController extends AbstractController
{
    #[Route('/admin/category', name: 'app_category_panel')]
    #[isGranted('ROLE_ADMIN')]
    public function categoryPanel(CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findAll();
        return $this->render('pages/admin/category_panel.html.twig', [
            'category' => $category,
        ]);
    }
}
