<?php

namespace App\Controller;

use App\Entity\Disponibilite;
use App\Form\DisponibiliteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/medecin')]
class MedecinDisponibiliteController extends AbstractController
{
    #[Route('/disponibilite', name: 'medecin_disponibilite')]
    public function disponibilite(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $medecin = $this->getUser();

        // 🔁 MODE ÉDITION ?
        $editId = $request->query->get('edit');
        if ($editId) {
            $disponibilite = $em->getRepository(Disponibilite::class)->find($editId);

            if (!$disponibilite || $disponibilite->getMedecin() !== $medecin) {
                throw $this->createAccessDeniedException();
            }
        } else {
            // ➕ MODE AJOUT
            $disponibilite = new Disponibilite();
            $disponibilite->setMedecin($medecin);
        }

        $form = $this->createForm(DisponibiliteType::class, $disponibilite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($disponibilite);
            $em->flush();

            $this->addFlash(
                'success',
                $editId ? 'Disponibilité modifiée avec succès' : 'Disponibilité ajoutée avec succès'
            );

            return $this->redirectToRoute('medecin_disponibilite');
        }

        return $this->render('medecin/disponibilite.html.twig', [
            'form' => $form->createView(),
            'disponibilites' => $medecin->getDisponibilites(),
            'editId' => $editId,
        ]);
    }

    #[Route('/disponibilite/{id}/delete', name: 'medecin_disponibilite_delete', methods: ['POST'])]
    public function delete(
        Disponibilite $disponibilite,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if ($disponibilite->getMedecin() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$disponibilite->getId(), $request->request->get('_token'))) {
            $em->remove($disponibilite);
            $em->flush();

            $this->addFlash('success', 'Disponibilité supprimée');
        }

        return $this->redirectToRoute('medecin_disponibilite');
    }
}
