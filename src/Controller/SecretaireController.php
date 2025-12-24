<?php

namespace App\Controller;

use App\Entity\Secretaire;
use App\Repository\RendezVousRepository;
use App\Repository\PatientRepository; // 
use App\Service\AbonnementAccessService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request; 
use App\Service\EmailService;

class SecretaireController extends AbstractController
{
    #[Route('/secretaire/dashboard', name: 'secretaire_dashboard')]
    public function dashboard(
        AbonnementAccessService $abonnementAccess,
         RendezVousRepository $rdvRepo,     
        PatientRepository $patientRepo 
    ): Response {
        /** @var Secretaire $secretaire */
        $secretaire = $this->getUser();

        // 🔐 Sécurité : vérifier que c’est bien une secrétaire
        if (!$secretaire instanceof Secretaire) {
            throw $this->createAccessDeniedException();
        }

        // 🔥 RÈGLE MÉTIER PRINCIPALE
       $medecin = $secretaire->getMedecin();
       // 📊 STATS DASHBOARD SECRÉTAIRE
$rdvEnAttente   = count($rdvRepo->findEnAttenteByMedecin($medecin));
$rdvConfirmes   = count($rdvRepo->findAcceptedByMedecin($medecin));
$rdvAnnules     = count($rdvRepo->findAnnulesByMedecin($medecin));
$rdvTodayList = $rdvRepo->findTodayByMedecin($medecin);
$rdvToday     = count($rdvTodayList);

// (optionnel mais pro)
$patientsCount  = $patientRepo->countByMedecin($medecin);


if (!$medecin || !$abonnementAccess->medecinHasAccess($medecin)) {
    $this->addFlash(
        'danger',
        'L’accès est désactivé : le médecin n’a pas d’abonnement actif.'
    );

    return $this->redirectToRoute('app_logout');
}

        return $this->render('secretaire/dashboard.html.twig', [
            'secretaire' => $secretaire,
            'medecin' => $medecin,
            'rdvEnAttente'   => $rdvEnAttente,
    'rdvConfirmes'   => $rdvConfirmes,
    'rdvAnnules'     => $rdvAnnules,
    'rdvToday'       => $rdvToday,
    'patientsCount' => $patientsCount,
    'rdvTodayList'   => $rdvTodayList
        ]);
    }
    #[Route('/secretaire/rdv/{id}/rappel', name: 'secretaire_rdv_rappel', methods: ['POST'])]
public function rappelRdv(
    int $id,
    Request $request,
    RendezVousRepository $rdvRepo,
    EmailService $emailService
): Response {
    $rdv = $rdvRepo->find($id);
    if (!$rdv) {
        throw $this->createNotFoundException();
    }

    if (!$this->isCsrfTokenValid('remind-rdv-'.$rdv->getId(), $request->request->get('_token'))) {
        throw $this->createAccessDeniedException('Token CSRF invalide');
    }

    $patient = $rdv->getPatient();
    if (!$patient || !$patient->getEmail()) {
        $this->addFlash('danger', "Le patient n'a pas d'email.");
        return $this->redirectToRoute('secretaire_dashboard');
    }

    // envoi email
   $emailService->sendRdvReminder(
    $patient->getEmail(),
    $patient->getPrenom(),
    $rdv
);

    $this->addFlash('success', '✅ Rappel envoyé au patient.');
    return $this->redirectToRoute('secretaire_dashboard');
}

}
