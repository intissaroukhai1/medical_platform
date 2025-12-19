<?php

namespace App\Controller;

use App\Entity\Specialite;
use App\Form\SpecialiteType;
use App\Repository\SpecialiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/specialites')]
class SpecialiteController extends AbstractController
{
    #[Route('/', name: 'specialite_index')]
    public function index(
        SpecialiteRepository $repo,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        // 🔐 sécurité backend
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // formulaire pour le modal
        $specialite = new Specialite();
        $form = $this->createForm(SpecialiteType::class, $specialite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($specialite);
            $em->flush();

            $this->addFlash('success', 'Spécialité ajoutée');
            return $this->redirectToRoute('specialite_index');
        }

        return $this->render('admin/specialite/index.html.twig', [
            'specialites' => $repo->findAll(),
            'form' => $form->createView(),
        ]);
    }

 #[Route('/{id}/edit', name: 'specialite_edit', methods: ['POST'])]
public function edit(
    Specialite $specialite,
    Request $request,
    EntityManagerInterface $em
): Response {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $form = $this->createForm(SpecialiteType::class, $specialite);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();
        $this->addFlash('success', 'Spécialité modifiée');
    }

    return $this->redirectToRoute('specialite_index');
}


   #[Route('/{id}/delete', name: 'specialite_delete', methods: ['POST'])]
public function delete(
    Request $request,
    Specialite $specialite,
    EntityManagerInterface $em
): Response {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    if ($this->isCsrfTokenValid(
        'delete'.$specialite->getId(),
        $request->request->get('_token')
    )) {
        $em->remove($specialite);
        $em->flush();
        $this->addFlash('success', 'Spécialité supprimée');
    }

    return $this->redirectToRoute('specialite_index');
}


}
