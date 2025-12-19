<?php

namespace App\Controller;

use App\Entity\Medecin;
use App\Entity\MedecinAbonnement;
use App\Repository\MedecinRepository;
use App\Repository\AbonnementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/medecins')]
class AdminMedecinController extends AbstractController
{
    /**
     * 📋 Liste des médecins
     */
    #[Route('/', name: 'admin_medecin_index', methods: ['GET'])]
    public function index(
        MedecinRepository $medecinRepo,
        AbonnementRepository $abonnementRepo
    ): Response {
        return $this->render('admin/medecin/index.html.twig', [
            'medecins'     => $medecinRepo->findAll(),
            'abonnements' => $abonnementRepo->findAll(),
        ]);
    }

    /**
     * 📦 Attribuer un abonnement à un médecin
     */
    #[Route('/{id}/abonnement', name: 'admin_medecin_abonnement', methods: ['POST'])]
    public function assignAbonnement(
        Medecin $medecin,
        Request $request,
        AbonnementRepository $abonnementRepo,
        EntityManagerInterface $em
    ): Response {
        $abonnementId = $request->request->get('abonnement_id');

        if (!$abonnementId) {
            $this->addFlash('danger', 'Veuillez choisir un abonnement.');
            return $this->redirectToRoute('admin_medecin_index');
        }

        $abonnement = $abonnementRepo->find($abonnementId);

        if (!$abonnement || !$abonnement->isActif()) {
            $this->addFlash('danger', 'Abonnement invalide.');
            return $this->redirectToRoute('admin_medecin_index');
        }

        // Expirer anciens abonnements
        foreach ($medecin->getAbonnements() as $old) {
            if ($old->isActif()) {
                $old->setStatut('EXPIRE');
                $old->setDateExpiration(new \DateTimeImmutable());
            }
        }

        // Nouveau abonnement
        $medecinAbonnement = new MedecinAbonnement();
        $medecinAbonnement
            ->setMedecin($medecin)
            ->setAbonnement($abonnement)
            ->setStatut('ACTIF');

        $em->persist($medecinAbonnement);
        $em->flush();

        $this->addFlash('success', 'Abonnement attribué avec succès.');

        return $this->redirectToRoute('admin_medecin_index');
    }
    #[Route('/{id}/abonnement/disable', name: 'admin_medecin_abonnement_disable', methods: ['POST'])]
public function disableAbonnement(
    Medecin $medecin,
    EntityManagerInterface $em
): Response {
    $active = $medecin->getActiveAbonnement();

    if (!$active) {
        $this->addFlash('warning', 'Aucun abonnement actif.');
        return $this->redirectToRoute('admin_medecin_index');
    }

    $active->setStatut('EXPIRE');
    $active->setDateExpiration(new \DateTimeImmutable());

    $em->flush();

    $this->addFlash('success', 'Abonnement désactivé avec succès.');

    return $this->redirectToRoute('admin_medecin_index');
}

}
