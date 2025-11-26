<?php

namespace App\Controller\User;

use App\Entity\Work;
use App\Repository\WorkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserFavoriteController extends AbstractController
{
    #[Route('/addToFavorite/{slug}', name: 'app_addToFavorite')]
    #[IsGranted('ROLE_USER')]
    public function addToFavorite(#[MapEntity(mapping: ['slug' => 'slug'])]Work $work, EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();
        if ($work->getFavorite()->contains($user)) {
            $work->removeFavorite($user);
            $this->addFlash('success', 'Retiré des favoris');
        } else {
            $work->addFavorite($user);
            $this->addFlash('success', 'Ajouté aux favoris');
        }
        $entityManager->flush();
        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/user/favorites', name: 'app_favorites')]
    #[IsGranted('ROLE_USER')]
    public function favorites(WorkRepository $works) : Response {
        $user = $this->getUser();
        $favorites = $works->UserFavoriteWork($user->getUsername());
        return $this->render('pages/user/favorite.html.twig', [
            'favorites' => $favorites,
        ]);
    }
}
