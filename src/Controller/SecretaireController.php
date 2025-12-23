<?php

namespace App\Controller;

use App\Entity\Secretaire;
use App\Service\AbonnementAccessService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecretaireController extends AbstractController
{
    #[Route('/secretaire/dashboard', name: 'secretaire_dashboard')]
    public function dashboard(
        AbonnementAccessService $abonnementAccess
    ): Response {
        /** @var Secretaire $secretaire */
        $secretaire = $this->getUser();

        // 🔐 Sécurité : vérifier que c’est bien une secrétaire
        if (!$secretaire instanceof Secretaire) {
            throw $this->createAccessDeniedException();
        }

        // 🔥 RÈGLE MÉTIER PRINCIPALE
       $medecin = $secretaire->getMedecin();

if (!$medecin || !$abonnementAccess->medecinHasAccess($medecin)) {
    $this->addFlash(
        'danger',
        'L’accès est désactivé : le médecin n’a pas d’abonnement actif.'
    );

    return $this->redirectToRoute('app_logout');
}

        return $this->render('secretaire/dashboard.html.twig', [
            'secretaire' => $secretaire,
            'medecin' => $medecin
        ]);
    }
}
