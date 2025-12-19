<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PatientController extends AbstractController
{
    #[Route('/patient/dashboard', name: 'patient_dashboard')]
    public function dashboard(): Response
    {
        $patient = $this->getUser(); // 👈 patient connecté

        return $this->render('patient/dashboard.html.twig', [
            'patient' => $patient
        ]);
    }
}
