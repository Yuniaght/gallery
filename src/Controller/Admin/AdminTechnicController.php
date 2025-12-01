<?php

namespace App\Controller\Admin;

use App\Entity\Technic;
use App\Form\TechnicType;
use App\Repository\TechnicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[isGranted('ROLE_ADMIN')]
final class AdminTechnicController extends AbstractController
{
    public function __construct(private readonly SluggerInterface $slugger)
    {

    }
    #[Route('/admin/technic', name: 'app_technic_panel')]
    public function TechnicPanel(TechnicRepository $technicRepository): Response
    {
        $technics = $technicRepository->findAll();
        return $this->render('pages/admin/technic_panel.html.twig', [
            'technics' => $technics,
        ]);
    }

    #[Route('/admin/new_technic', name: 'app_new_technic')]
    public function newTechnic (EntityManagerInterface $entityManager, Request $request): Response
    {
        $technic = new Technic();
        $form = $this->createForm(TechnicType::class, $technic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $technic->setSlug($this->slugger->slug($technic->getName()));
            $entityManager->persist($technic);
            $entityManager->flush();
            $this->addFlash('success', 'La catégorie a été ajouté');
            return $this->redirectToRoute('app_technic_panel');
        }
        return $this->render('pages/admin/technic_form.html.twig', [
            'newTechnicForm' => $form,
        ]);
    }
}
