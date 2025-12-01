<?php

namespace App\Controller\Admin;

use App\Entity\Artist;
use App\Form\ArtistType;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[isGranted('ROLE_ADMIN')]
final class AdminArtistController extends AbstractController
{
    public function __construct(private readonly SluggerInterface $slugger)
    {

    }

    #[Route('/admin/artists', name: 'app_artists_panel')]
    public function artistPanel(ArtistRepository $artistRepository): Response
    {
        $artists = $artistRepository->findAll();
        return $this->render('pages/admin/artist_panel.html.twig', [
            'artists' => $artists,
        ]);
    }

    #[Route('/admin/new_artist', name: 'app_new_artist')]
    public function newArtist (Request $request, EntityManagerInterface $entityManager): Response
    {
        $artist = new Artist();
        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$artist->getImageFile() && !$artist->getImage()) {
                $artist->setImage('default_artist.jpg');
            }
            $artist->setAddedAt(new \DateTimeImmutable());
            $artist->setEditedAt(new \DateTimeImmutable());
            $artist->setSlug($this->slugger->slug($artist->getFirstName() .$artist->getLastName()));
            $entityManager->persist($artist);
            $entityManager->flush();
            $this->addFlash('success', 'L\'artiste a été ajouté');
            return $this->redirectToRoute('app_artists_panel');
        }
        return $this->render('pages/admin/artist_form.html.twig', [
            'newArtistForm' => $form,
        ]);
    }

    #[Route('/admin/editArtist/{id}', name: 'app_edit_artist')]
    public function editArtist (Artist $artist, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $artist->setEditedAt(new \DateTimeImmutable());
            $entityManager->persist($artist);
            $entityManager->flush();
            $this->addFlash('success', 'L\'artiste a été modifié');
            return $this->redirectToRoute('app_artists_panel');
        }
        return $this->render('pages/admin/artist_form.html.twig', [
            'newArtistForm' => $form,
        ]);
    }

    #[Route('/admin/deleteArtist/{id}', name: 'app_delete_artist')]
    public function deleteArtist (Artist $artist, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($artist);
        $entityManager->flush();
        $this->addFlash('success', 'L\'artiste a été supprimé');
        return $this->redirectToRoute('app_artists_panel');
    }
}
