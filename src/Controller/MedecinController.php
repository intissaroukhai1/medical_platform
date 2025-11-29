<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MedecinController extends AbstractController
{
    #[Route('/medecin/dashboard', name: 'medecin_dashboard')]
    public function dashboard()
    {
        return $this->render('medecin/dashboard.html.twig');
    }
}
