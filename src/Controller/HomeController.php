<?php

namespace App\Controller;

use App\Repository\SpecialiteRepository;
use App\Repository\MedecinRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        Request $request,
        SpecialiteRepository $specialiteRepository,
        MedecinRepository $medecinRepository
    ): Response {

        // 1️⃣ Charger les spécialités
        $specialites = $specialiteRepository->findAll();

        // 2️⃣ Lire paramètres GPS + spécialité
        $latitude     = $request->query->get('latitude');
        $longitude    = $request->query->get('longitude');
        $specialiteId = $request->query->get('specialite');

        $medecins = [];

        // 3️⃣ Recherche "près de moi"
        if ($latitude && $longitude) {

            $results = $medecinRepository->searchMedecins(
                $specialiteId ?: null,
                (float) $latitude,
                (float) $longitude,
                20 // rayon en km
            );

            foreach ($results as $row) {
                if (is_array($row)) {
                    $medecin = $row[0];
                    $medecin->distance = round($row['distance'], 1);
                    $medecins[] = $medecin;
                } else {
                    $medecins[] = $row;
                }
            }
        }

        // 4️⃣ Retourner la vue
        return $this->render('home/index.html.twig', [
            'specialites' => $specialites,
            'medecins'    => $medecins,
        ]);
    }
}
