<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SecretaireController extends AbstractController
{
    #[Route('/secretaire/dashboard', name: 'secretaire_dashboard')]
    public function dashboard()
    {
        return $this->render('secretaire/dashboard.html.twig');
    }
}
