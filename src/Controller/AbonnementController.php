<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Form\AbonnementType;
use App\Repository\AbonnementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/abonnements')]
class AbonnementController extends AbstractController
{
    #[Route('/', name: 'abonnement_index')]
    public function index(
        AbonnementRepository $repo,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $abonnement = new Abonnement();
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        // ➕ CREATE (depuis le modal)
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($abonnement);
            $em->flush();

            return $this->redirectToRoute('abonnement_index');
        }

        return $this->render('admin/abonnement/index.html.twig', [
            'abonnements' => $repo->findAll(),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'abonnement_edit', methods: ['POST'])]
    public function edit(
        Abonnement $abonnement,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        // ✏️ UPDATE (depuis le modal)
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
        }

        return $this->redirectToRoute('abonnement_index');
    }

    #[Route('/{id}/toggle', name: 'abonnement_toggle')]
    public function toggle(
        Abonnement $abonnement,
        EntityManagerInterface $em
    ): Response {
        $abonnement->setActif(!$abonnement->isActif());
        $em->flush();

        return $this->redirectToRoute('abonnement_index');
    }
}
