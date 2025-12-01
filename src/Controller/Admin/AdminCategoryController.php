<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[isGranted('ROLE_ADMIN')]
final class AdminCategoryController extends AbstractController
{
    public function __construct(private readonly SluggerInterface $slugger)
    {

    }
    #[Route('/admin/category', name: 'app_category_panel')]
    public function categoryPanel(CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findAll();
        return $this->render('pages/admin/category_panel.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/admin/new_category', name: 'app_new_category')]
    public function newArtist (Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug($this->slugger->slug($category->getName()));
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success', 'La catégorie a été ajouté');
            return $this->redirectToRoute('app_category_panel');
        }
        return $this->render('pages/admin/category_form.html.twig', [
            'newCategoryForm' => $form,
        ]);
    }
}
