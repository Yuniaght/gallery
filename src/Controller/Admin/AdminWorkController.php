<?php

namespace App\Controller\Admin;

use App\Entity\Work;
use App\Form\WorkType;
use App\Repository\WorkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[isGranted('ROLE_ADMIN')]
final class AdminWorkController extends AbstractController
{
    public function __construct(private readonly SluggerInterface $slugger)
    {

    }

    #[Route('/admin/works', name: 'app_works_panel')]
    public function workPanel(WorkRepository $workRepository): Response
    {
        $works = $workRepository->findAll();
        return $this->render('pages/admin/work_panel.html.twig', [
            'works' => $works,
        ]);
    }

    #[Route('/admin/new_work', name: 'app_new_work')]
    public function newWork (Request $request, EntityManagerInterface $entityManager): Response
    {
        $work = new Work();
        $form = $this->createForm(WorkType::class, $work);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$work->getImageFile() && !$work->getImage()) {
                $work->setImage('default_work.jpg');
            }
            $work->setAddedAt(new \DateTimeImmutable());
            $work->setEditedAt(new \DateTimeImmutable());
            $work->setSlug($this->slugger->slug($work->getTitle()));
            $entityManager->persist($work);
            $entityManager->flush();
            $this->addFlash('success', 'L\'oeuvre a été ajouté');
            return $this->redirectToRoute('app_works_panel');
        }
        return $this->render('pages/admin/work_form.html.twig', [
            'newWorkForm' => $form,
        ]);
    }

    #[Route('/admin/edit_work/{id}', name: 'app_edit_work')]
    public function editWork (Work $work, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WorkType::class, $work);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $work->setEditedAt(new \DateTimeImmutable());
            $entityManager->persist($work);
            $entityManager->flush();
            $this->addFlash('success', 'L\'oeuvre a été modifiée');
            return $this->redirectToRoute('app_works_panel');
        }
        return $this->render('pages/admin/work_form.html.twig', [
            'newWorkForm' => $form,
        ]);
    }

    #[Route('/admin/deleteWork/{id}', name: 'app_delete_work')]
    public function deleteArtist (Work $work, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($work);
        $entityManager->flush();
        $this->addFlash('success', 'L\'artiste a été supprimé');
        return $this->redirectToRoute('app_artists_panel');
    }
}
