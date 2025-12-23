<?php

namespace App\Controller;

use App\Entity\Medecin;
use App\Entity\Ordonnance;
use App\Entity\RendezVous;
use App\Form\OrdonnanceType;
use App\Repository\OrdonnanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_MEDECIN')]
#[Route('/medecin/ordonnance')]
class OrdonnanceController extends AbstractController
{
    /**
     * 🟢 Créer une ordonnance à partir d’un rendez-vous
     * URL : /medecin/ordonnance/new/{id}
     */
    #[Route('/new/{id}', name: 'medecin_ordonnance_new', methods: ['GET', 'POST'])]
    public function newFromRdv(
        RendezVous $rdv,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        /** @var Medecin $medecin */
        $medecin = $this->getUser();

        // 🔒 Sécurité : le RDV doit appartenir au médecin connecté
        if ($rdv->getMedecin()->getId() !== $medecin->getId()) {
            throw $this->createAccessDeniedException('Accès interdit');
        }

        // 🧾 Création ordonnance
        $ordonnance = new Ordonnance();
        $ordonnance->setMedecin($medecin);
        $ordonnance->setPatient($rdv->getPatient());
        $ordonnance->setRendezVous($rdv);

        $form = $this->createForm(OrdonnanceType::class, $ordonnance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ordonnance);
            $em->flush();

            $this->addFlash('success', 'Ordonnance créée avec succès');

            // 🔁 Retour à l’agenda (logique métier)
            return $this->redirectToRoute('medecin_agenda');
        }

        return $this->render('medecin/ordonnance.html.twig', [
            'form' => $form,
            'rdv'  => $rdv,
        ]);
    }

    /**
     * 🟢 Voir toutes les ordonnances liées à un rendez-vous
     * (optionnel mais très pro)
     * URL : /medecin/ordonnance/rdv/{id}
     */
    #[Route('/rdv/{id}', name: 'medecin_ordonnance_by_rdv', methods: ['GET'])]
    public function listByRdv(
        RendezVous $rdv,
        OrdonnanceRepository $ordonnanceRepo
    ): Response {
        /** @var Medecin $medecin */
        $medecin = $this->getUser();

        // 🔒 Sécurité
        if ($rdv->getMedecin()->getId() !== $medecin->getId()) {
            throw $this->createAccessDeniedException();
        }

        $ordonnances = $ordonnanceRepo->findBy(
            ['rendezVous' => $rdv],
            ['createdAt' => 'DESC']
        );

        return $this->render('medecin/ordonnance.html.twig', [
            'rdv' => $rdv,
            'ordonnances' => $ordonnances,
        ]);
    }
    #[Route('/edit/{id}', name: 'medecin_ordonnance_edit', methods: ['GET', 'POST'])]
public function edit(
    Ordonnance $ordonnance,
    Request $request,
    EntityManagerInterface $em
): Response {
    /** @var Medecin $medecin */
    $medecin = $this->getUser();

    // 🔒 Sécurité : seul le médecin créateur peut modifier
    if ($ordonnance->getMedecin()->getId() !== $medecin->getId()) {
        throw $this->createAccessDeniedException();
    }

    $form = $this->createForm(OrdonnanceType::class, $ordonnance);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();

        $this->addFlash('success', 'Ordonnance modifiée avec succès');

        return $this->redirectToRoute(
            'medecin_ordonnance_by_rdv',
            ['id' => $ordonnance->getRendezVous()->getId()]
        );
    }

    return $this->render('medecin/ordonnance.html.twig', [
        'form' => $form->createView(),
        'ordonnance' => $ordonnance,
    ]);
}
#[Route('/delete/{id}', name: 'medecin_ordonnance_delete', methods: ['POST'])]
public function delete(
    Ordonnance $ordonnance,
    Request $request,
    EntityManagerInterface $em
): Response {
    /** @var Medecin $medecin */
    $medecin = $this->getUser();

    // 🔒 Sécurité : seul le médecin créateur
    if ($ordonnance->getMedecin()->getId() !== $medecin->getId()) {
        throw $this->createAccessDeniedException();
    }

    // 🔐 Vérification CSRF
    if ($this->isCsrfTokenValid('delete_ordonnance_' . $ordonnance->getId(), $request->request->get('_token'))) {
        $rdvId = $ordonnance->getRendezVous()->getId();

        $em->remove($ordonnance);
        $em->flush();

        $this->addFlash('success', '🗑️ Ordonnance supprimée avec succès');

        return $this->redirectToRoute('medecin_ordonnance_by_rdv', [
            'id' => $rdvId
        ]);
    }

    throw $this->createAccessDeniedException('Token CSRF invalide');
}
#[Route('/print/{id}', name: 'medecin_ordonnance_print', methods: ['GET'])]
public function print(
    Ordonnance $ordonnance
): Response {
    /** @var \App\Entity\Medecin $medecin */
    $medecin = $this->getUser();

    // 🔒 Sécurité : le médecin ne peut imprimer que SES ordonnances
    if ($ordonnance->getMedecin()->getId() !== $medecin->getId()) {
        throw $this->createAccessDeniedException();
    }

    return $this->render('medecin/ordonnance_print.html.twig', [
        'ordonnance' => $ordonnance,
    ]);
}

}
