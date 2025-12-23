<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Repository\RendezVousRepository;
use App\Repository\OrdonnanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Dompdf\Dompdf;
use Dompdf\Options;

#[IsGranted('ROLE_MEDECIN')]
#[Route('/medecin/patient')]
class DossierMedicalController extends AbstractController
{
   #[Route('/{id}/dossier', name: 'medecin_patient_dossier', methods: ['GET'])]
public function dossier(
    Patient $patient,
    RendezVousRepository $rdvRepository,
    OrdonnanceRepository $ordonnanceRepository
): Response {
    /** @var Medecin $medecin */
    $medecin = $this->getUser();

    // 🔐 Vérifier relation médecin ↔ patient
    $hasAccess = $rdvRepository->count([
        'patient' => $patient,
        'medecin' => $medecin
    ]) > 0;

    if (!$hasAccess) {
        throw $this->createAccessDeniedException('Accès au dossier interdit');
    }

    // ✅ RDV du patient AVEC CE MÉDECIN
    $rdvs = $rdvRepository->findBy(
        ['patient' => $patient, 'medecin' => $medecin],
        ['date' => 'DESC']
    );

    // ✅ Ordonnances de CE MÉDECIN
    $ordonnances = $ordonnanceRepository->findBy(
        ['patient' => $patient, 'medecin' => $medecin],
        ['createdAt' => 'DESC']
    );

    return $this->render('medecin/dossier_medical.html.twig', [
        'patient' => $patient,
        'rdvs' => $rdvs,
        'ordonnances' => $ordonnances,
    ]);
}
#[Route('/{id}/dossier/pdf', name: 'medecin_patient_dossier_pdf', methods: ['GET'])]
public function dossierPdf(
    Patient $patient,
    RendezVousRepository $rendezVousRepository,
    OrdonnanceRepository $ordonnanceRepository
): Response {
    /** @var \App\Entity\Medecin $medecin */
    $medecin = $this->getUser();

    // 🔐 Sécurité : le médecin doit avoir au moins un RDV avec ce patient
    $hasAccess = $rendezVousRepository->count([
        'patient' => $patient,
        'medecin' => $medecin,
    ]) > 0;

    if (!$hasAccess) {
        throw $this->createAccessDeniedException();
    }

    $rdvs = $rendezVousRepository->findBy(
        ['patient' => $patient, 'medecin' => $medecin],
        ['date' => 'DESC']
    );

    $ordonnances = $ordonnanceRepository->findBy(
        ['patient' => $patient, 'medecin' => $medecin],
        ['createdAt' => 'DESC']
    );

    $html = $this->renderView('medecin/dossier_medical_pdf.html.twig', [
        'patient' => $patient,
        'rdvs' => $rdvs,
        'ordonnances' => $ordonnances,
        'medecin' => $medecin,
    ]);

    $options = new Options();
    $options->set('defaultFont', 'DejaVu Sans');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4');
    $dompdf->render();

    return new Response(
        $dompdf->output(),
        200,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="dossier_medical_'.$patient->getId().'.pdf"',
        ]
    );
}
}