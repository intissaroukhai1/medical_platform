<?php

namespace App\Controller;
use App\Repository\RendezVousRepository;
use App\Repository\OrdonnanceRepository;
use App\Entity\RendezVous;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PatientController extends AbstractController
{
    #[Route('/patient/dashboard', name: 'patient_dashboard')]
    public function dashboard(EntityManagerInterface $em,RendezVousRepository $rdvRepo,
    OrdonnanceRepository $ordonnanceRepo,): Response
    {
        $patient = $this->getUser(); // 👈 patient connecté
        if (!$patient) {
    throw $this->createAccessDeniedException();
}

// 📅 Rendez-vous à venir ce mois
$rdvCount = $rdvRepo->countUpcomingForPatientThisMonth($patient);

// 👨‍⚕️ Médecins distincts suivis
$medecinsCount = $rdvRepo->countDistinctMedecinsForPatient($patient);

// 💊 Ordonnances actives
$ordonnancesCount = $ordonnanceRepo->countForPatient($patient);

        // ✅ Récupérer les rendez-vous du patient connecté
        $rendezvous = $em->getRepository(RendezVous::class)->findBy(
            ['patient' => $patient],
            ['date' => 'ASC']
        );

        return $this->render('patient/dashboard.html.twig', [
            'patient' => $patient,
            'rendezvous' => $rendezvous, // 👈 IMPORTANT
            // 🔢 Cartes dynamiques
    'rdvCount'           => $rdvCount,
    'medecinsCount'      => $medecinsCount,
    'ordonnancesCount'   => $ordonnancesCount,
        ]);
    }
}
