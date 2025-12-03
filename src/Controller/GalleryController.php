<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\WorkRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function works(WorkRepository $work,CommentRepository $comment,Request $request, EntityManagerInterface $emi, array $slug): Response
    {
        $user = $this->getUser();
        $newComment = new Comment();
        $form = $this->createForm(CommentType::class, $newComment);
        $work = $work->findOneBy(['slug' => $slug]);
        $form->handleRequest($request);
        $userHasCommented = false;
        $commentPending = false;
        $userComment = null;
        if ($user) {
            $hasCommented = $comment->findOneBy([
                'work' => $work,
                'user' => $user
            ]);
            if ($hasCommented) {
                $userHasCommented = true;
                $userComment = $hasCommented;
                if (!$hasCommented->isPublic())
                    $commentPending = true;
            }
        }
        if ($user && !$userHasCommented && $form->isSubmitted() && $form->isValid()) {
            $newComment->setUser($user);
            $newComment->setWork($work);
            $newComment->setPublishedAt(new \DateTimeImmutable());
            $newComment->setIsPublic(false);
            $emi->persist($newComment);
            $emi->flush();
            $this->addFlash('success', 'Votre commentaire a bien été envoyé !');
            return $this->redirectToRoute('app_work', ['slug' => $work->getSlug()]);
        }
        return $this->render('pages/work.html.twig', [
            'work' => $work,
            'user_has_commented' => $userHasCommented,
            'comment_pending' => $commentPending,
            'user_comment' => $userComment,
            'commentForm' => $form->createView(),
        ]);
    }
}
