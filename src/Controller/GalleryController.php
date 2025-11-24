<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\WorkRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GalleryController extends AbstractController
{
    #[Route('/gallery', name: 'app_gallery')]
    public function gallery(WorkRepository $works, Request $request, PaginatorInterface $paginator): Response
    {
        $works = $works->knpFindAll();
        $pagination = $paginator->paginate($works, $request->query->getInt('page', 1), 9,
        [
            'defaultSortFieldName' => 'works.createdAt',
            'defaultSortDirection' => 'desc',
        ]);
        return $this->render('pages/gallery.html.twig', [
            'works' => $pagination,
        ]);
    }

    #[Route('/gallery/{slug:slug}', name: 'app_galleryByCategory')]
    public function galleryByCategory(WorkRepository $works, CategoryRepository $category, Request $request, PaginatorInterface $paginator,array $slug): Response
    {
        $chosenCategory = $category->findOneBy(['slug' => $slug]);
        $works = $works->knpFindByCategory($chosenCategory);
        $pagination = $paginator->paginate($works, $request->query->getInt('page', 1), 9);
        return $this->render('pages/gallery.html.twig', [
            'works' => $pagination,
        ]);
    }

    #[Route('/gallery/view/{slug:slug}', name: 'app_work')]
    public function works(WorkRepository $work,array $slug): Response
    {
        $work = $work->findOneBy(['slug' => $slug]);
        return $this->render('pages/work.html.twig', [
            'work' => $work,
        ]);
    }
}
