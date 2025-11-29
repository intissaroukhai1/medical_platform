<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PatientController extends AbstractController
{
    #[Route('/patient/dashboard', name: 'patient_dashboard')]
    public function dashboard()
    {
        return $this->render('patient/dashboard.html.twig');
    }
}
