<?php

namespace App\Controller;

use App\Repository\SpecialiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(SpecialiteRepository $specialiteRepository): Response
    {
        // Récupérer toutes les spécialités
        $specialites = $specialiteRepository->findAll();

        // Retourner la vue avec les données
        return $this->render('home/index.html.twig', [
            'specialites' => $specialites,
        ]);
    }
}
