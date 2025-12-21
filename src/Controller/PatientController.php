<?php

namespace App\Controller;

use App\Entity\RendezVous;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PatientController extends AbstractController
{
    #[Route('/patient/dashboard', name: 'patient_dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        $patient = $this->getUser(); // 👈 patient connecté

        // ✅ Récupérer les rendez-vous du patient connecté
        $rendezvous = $em->getRepository(RendezVous::class)->findBy(
            ['patient' => $patient],
            ['date' => 'ASC']
        );

        return $this->render('patient/dashboard.html.twig', [
            'patient' => $patient,
            'rendezvous' => $rendezvous, // 👈 IMPORTANT
        ]);
    }
}
