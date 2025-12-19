<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Medecin;

class MedecinController extends AbstractController
{
    #[Route('/medecin/dashboard', name: 'medecin_dashboard')]
    public function dashboard(): Response
    {
        $medecin = $this->getUser();   // 🔥 Médecin connecté

        return $this->render('medecin/dashboard.html.twig', [
            "medecin" => $medecin,      // 🔥 ENVOYER LA VARIABLE
            "patients" => [],
            "rdv_today" => [],
            "ordonnances" => []
        ]);
    }

    #[Route('/api/medecins', name: 'api_medecins', methods: ['GET'])]
    public function getMedecins(EntityManagerInterface $em): JsonResponse
    {
        $medecins = $em->getRepository(Medecin::class)->findAll();

        $data = [];

        foreach ($medecins as $med) {
            $data[] = [
                'id' => $med->getId(),
                'nom' => $med->getNom(),
                'prenom' => $med->getPrenom(),
            ];
        }

        return new JsonResponse($data);
    }
}
