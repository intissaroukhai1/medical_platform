<?php

namespace App\Controller;

use App\Entity\RendezVous;
use App\Entity\Specialite;
use App\Form\RendezVousType;
use App\Repository\MedecinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_PATIENT')]
#[Route('/patient/rendezvous')]
class PatientRendezVousController extends AbstractController
{
    #[Route('', name: 'patient_rendezvous', methods: ['GET', 'POST'])]
    
    public function index(
        Request $request,
        EntityManagerInterface $em,
        MedecinRepository $medecinRepository
    ): Response {

        // 🔐 Sécurité
        $patient = $this->getUser();
        if (!$patient) {
            throw $this->createAccessDeniedException();
        }

        // 🧩 Entité RDV
        $rendezVous = new RendezVous();

        // 🔍 Médecins filtrés (vide par défaut)
        $medecins = [];

        /**
         * ✅ IMPORTANT
         * On lit la spécialité depuis POST
         * car le champ specialite est mapped => false
         */
        $formData = $request->request->all('rendez_vous');

        if (isset($formData['specialite']) && $formData['specialite']) {
            $specialite = $em
                ->getRepository(Specialite::class)
                ->find($formData['specialite']);

            if ($specialite) {
                $medecins = $medecinRepository->findBySpecialite($specialite);
            }
        }

        // 🧾 UN SEUL FORMULAIRE (clé de la solution)
        $form = $this->createForm(RendezVousType::class, $rendezVous, [
            'medecins' => $medecins,
        ]);
        $form->handleRequest($request);

        // 📅 Création du rendez-vous
       if (
    $request->request->has('create')
    && $form->isSubmitted()
    && $form->isValid()
) {
            $rendezVous->setPatient($patient);
            $rendezVous->setStatut(RendezVous::STATUT_EN_ATTENTE);

            $em->persist($rendezVous);
            $em->flush();

            $this->addFlash('rdv_success', 'Rendez-vous créé avec succès.');

            return $this->redirectToRoute('patient_rendezvous');
        }

        // 📋 Liste des RDV du patient
        $rendezvous = $em->getRepository(RendezVous::class)->findBy(
            ['patient' => $patient],
            ['date' => 'DESC']
        );

        return $this->render('patient/rendezvous.html.twig', [
            'form' => $form->createView(),
            'rendezvous' => $rendezvous,
        ]);
    }
}
