<?php

namespace App\Controller;

use App\Entity\RendezVous;
use App\Entity\Secretaire;
use App\Form\SecretaireRendezVousType;
use App\Repository\RendezVousRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SECRETAIRE')]
#[Route('/secretaire')]
class SecretaireRendezVousController extends AbstractController
{
    /**
     * LISTE + CRÉATION
     */
    #[Route('/rendez-vous', name: 'secretaire_rendez_vous', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        RendezVousRepository $repo,
        EntityManagerInterface $em
    ): Response {

        /** @var Secretaire $secretaire */
        $secretaire = $this->getUser();
        $medecin = $secretaire->getMedecin();

        if (!$medecin) {
            throw $this->createAccessDeniedException(
                'Aucun médecin associé à ce secrétaire.'
            );
        }

        // 🔹 Liste des RDV du médecin
        $rendezvous = $repo->findBy(
            ['medecin' => $medecin],
            ['date' => 'DESC']
        );

        // 🔹 Nouveau RDV
        $rendezVous = new RendezVous();
        $rendezVous->setMedecin($medecin);

        $form = $this->createForm(SecretaireRendezVousType::class, $rendezVous);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Sécurité + valeur par défaut
            $rendezVous->setMedecin($medecin);
            $rendezVous->setStatut(RendezVous::STATUT_EN_ATTENTE);

            $em->persist($rendezVous);
            $em->flush();

            $this->addFlash('success', 'Rendez-vous créé avec succès ✅');

            return $this->redirectToRoute('secretaire_rendez_vous');
        }

        return $this->render('secretaire/rendezvous.html.twig', [
            'rendezvous' => $rendezvous,
            'form'       => $form->createView(),
            'editRdv'    => null,
        ]);
    }

    /**
     * MODIFICATION
     */
    #[Route('/rendez-vous/{id}/edit', name: 'secretaire_rdv_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        RendezVous $rendezVous,
        RendezVousRepository $repo,
        EntityManagerInterface $em
    ): Response {

        /** @var Secretaire $secretaire */
        $secretaire = $this->getUser();
        $medecin = $secretaire->getMedecin();

        // Sécurité : le secrétaire ne modifie que SES RDV
        if ($rendezVous->getMedecin() !== $medecin) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(SecretaireRendezVousType::class, $rendezVous);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $rendezVous->setMedecin($medecin);
            $em->flush();

            $this->addFlash('success', 'Rendez-vous modifié ✅');

            return $this->redirectToRoute('secretaire_rendez_vous');
        }

        return $this->render('secretaire/rendezvous.html.twig', [
            'rendezvous' => $repo->findBy(
                ['medecin' => $medecin],
                ['date' => 'DESC']
            ),
            'form'    => $form->createView(),
            'editRdv' => $rendezVous,
        ]);
    }

    /**
     * ANNULATION
     */
    #[Route('/rendez-vous/{id}/annuler', name: 'secretaire_rdv_annuler', methods: ['POST'])]
    public function annuler(
        RendezVous $rendezVous,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        if (!$this->isCsrfTokenValid(
            'cancel-rdv-' . $rendezVous->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException();
        }

        $rendezVous->setStatut(RendezVous::STATUT_ANNULE);
        $em->flush();

        $this->addFlash('warning', 'Rendez-vous annulé ⚠️');

        return $this->redirectToRoute('secretaire_rendez_vous');
    }

    /**
     * SUPPRESSION
     */
    #[Route('/rendez-vous/{id}/delete', name: 'secretaire_rdv_delete', methods: ['POST'])]
    public function delete(
        RendezVous $rendezVous,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        if (!$this->isCsrfTokenValid(
            'delete-rdv-' . $rendezVous->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($rendezVous);
        $em->flush();

        $this->addFlash('danger', 'Rendez-vous supprimé 🗑️');

        return $this->redirectToRoute('secretaire_rendez_vous');
    }
    #[Route('/rendez-vous/{id}/confirmer', name: 'secretaire_rdv_confirmer', methods: ['POST'])]
public function confirmer(
    RendezVous $rendezVous,
    Request $request,
    EntityManagerInterface $em
): Response {
    if (!$this->isCsrfTokenValid(
        'confirm-rdv-' . $rendezVous->getId(),
        $request->request->get('_token')
    )) {
        throw $this->createAccessDeniedException();
    }

    $rendezVous->setStatut(RendezVous::STATUT_CONFIRME);
    $em->flush();

    $this->addFlash('success', 'Rendez-vous confirmé ✅');

    return $this->redirectToRoute('secretaire_rendez_vous');
}

}
