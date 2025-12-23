<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Form\SecretairePatientType;
use App\Repository\PatientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SECRETAIRE')]
#[Route('/secretaire/patients')]
class SecretairePatientController extends AbstractController
{
    #[Route('', name: 'secretaire_patients', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        PatientRepository $repo,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {

        /** @var \App\Entity\Secretaire $secretaire */
        $secretaire = $this->getUser();
        $medecin = $secretaire->getMedecin();

        if (!$medecin) {
            throw $this->createAccessDeniedException();
        }

        $patient = new Patient();
        $form = $this->createForm(SecretairePatientType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ⚠️ temporaire (à améliorer plus tard)
            $patient->setNumeroCin('TEMP-' . uniqid());

            // 🔐 mot de passe temporaire
            $temporaryPassword = bin2hex(random_bytes(10));
            $patient->setPassword(
                $passwordHasher->hashPassword($patient, $temporaryPassword)
            );

            // 👤 rôle
            $patient->setRoles(['ROLE_PATIENT']);

            // 🔥 liaison OBLIGATOIRE
            $patient->setMedecin($medecin);

            $em->persist($patient);
            $em->flush();

            $this->addFlash('success', 'Patient ajouté avec succès ✅');

            return $this->redirectToRoute('secretaire_patients');
        }

        return $this->render('secretaire/patient.html.twig', [
            'patients' => $repo->findPatientsForSecretaireMedecin($medecin),
            'form'     => $form->createView(),
            'edit'     => null,
        ]);
    }

    #[Route('/{id}/edit', name: 'secretaire_patient_edit', methods: ['GET', 'POST'])]
    public function edit(
        Patient $patient,
        Request $request,
        PatientRepository $repo,
        EntityManagerInterface $em
    ): Response {

        /** @var \App\Entity\Secretaire $secretaire */
        $secretaire = $this->getUser();
        $medecin = $secretaire->getMedecin();

        if (!$medecin || $patient->getMedecin() !== $medecin) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(SecretairePatientType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Patient modifié avec succès ✏️');
            return $this->redirectToRoute('secretaire_patients');
        }

        return $this->render('secretaire/patient.html.twig', [
            'patients' => $repo->findPatientsForSecretaireMedecin($medecin),
            'form'     => $form->createView(),
            'edit'     => $patient,
        ]);
    }

    #[Route('/{id}/delete', name: 'secretaire_patient_delete', methods: ['POST'])]
    public function delete(
        Patient $patient,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        if (!$this->isCsrfTokenValid(
            'delete-patient-' . $patient->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($patient);
        $em->flush();

        $this->addFlash('danger', 'Patient supprimé 🗑️');

        return $this->redirectToRoute('secretaire_patients');
    }
}
