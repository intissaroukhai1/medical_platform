<?php

namespace App\Controller;

use App\Entity\Medecin;
use App\Entity\RendezVous;
use App\Repository\RendezVousRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;


#[IsGranted('ROLE_MEDECIN')]
#[Route('/medecin')]
class MedecinAgendaController extends AbstractController
{
   #[Route('/agenda', name: 'medecin_agenda', methods: ['GET'])]
public function agenda(RendezVousRepository $rdvRepo): Response
{
    /** @var Medecin $medecin */
    $medecin = $this->getUser();

    if (!$medecin instanceof Medecin) {
        throw $this->createAccessDeniedException('Accès réservé au médecin');
    }

    // ✅ RDV confirmés aujourd’hui
    $rdvsToday = $rdvRepo->findTodayAcceptedByMedecin($medecin);

    // ✅ Tous les RDV confirmés
    $rdvsAll = $rdvRepo->findAcceptedByMedecin($medecin);

    return $this->render('medecin/agenda.html.twig', [
        'medecin'   => $medecin,
        'rdvsToday' => $rdvsToday, // 🔥 OBLIGATOIRE
        'rdvsAll'   => $rdvsAll,   // 🔥 OBLIGATOIRE
    ]);
}
#[Route('/rdv/{id}/details', name: 'medecin_rdv_details', methods: ['GET'])]
public function rdvDetails(RendezVous $rdv): JsonResponse
{
    /** @var Medecin $medecin */
    $medecin = $this->getUser();

    // ✅ Vérifier que le RDV appartient bien à ce médecin
    if ($rdv->getMedecin()->getId() !== $medecin->getId()) {
        throw $this->createAccessDeniedException('Ce rendez-vous ne vous appartient pas');
    }

    // ✅ Retourner les données en JSON pour le modal
    return $this->json([
        'id' => $rdv->getId(),
        'patient' => [
            'nom' => $rdv->getPatient()->getNom(),
            'prenom' => $rdv->getPatient()->getPrenom(),
            'email' => $rdv->getPatient()->getEmail() ?? 'Non renseigné',
            'telephone' => $rdv->getPatient()->getTelephone() ?? 'Non renseigné',
        ],
        'date' => $rdv->getDate()->format('d/m/Y'),
        'heure' => $rdv->getDate()->format('H:i'),
        'mode' => $rdv->getMode(),
        'statut' => $rdv->getStatut(),
        'motif' => $rdv->getMotif() ?? 'Non spécifié',
        'notes' => $rdv->getNotes() ?? '',
    ]);
}
}