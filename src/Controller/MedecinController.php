<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Medecin;
use Symfony\Component\HttpFoundation\Request;
use App\Form\MedecinProfileType;


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

  #[Route('/agenda', name: 'medecin_agenda')]
    public function agenda(): Response
    {
        return $this->render('medecin/agenda.html.twig');
    }

    #[Route('/patients', name: 'medecin_patients')]
    public function patients(): Response
    {
        return $this->render('medecin/patients.html.twig');
    }

    #[Route('/ordonnances', name: 'medecin_ordonnances')]
    public function ordonnances(): Response
    {
        return $this->render('medecin/ordonnances.html.twig');
    }

    #[Route('/disponibilite', name: 'medecin_disponibilite')]
    public function disponibilite(): Response
    {
        return $this->render('medecin/disponibilite.html.twig');
    }
    
    #[Route('/medecin/profil', name: 'medecin_profil')]
    public function profil(Request $request, EntityManagerInterface $em): Response
    {
        /** @var Medecin $medecin */
        $medecin = $this->getUser();

        if (!$medecin instanceof Medecin) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(MedecinProfileType::class, $medecin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photo = $form->get('photoProfil')->getData();
            if ($photo) {
                $filename = uniqid().'.'.$photo->guessExtension();
                $photo->move(
                    $this->getParameter('photo_directory'),
                    $filename
                );
                $medecin->setPhotoProfil($filename);
            }

            $em->flush();
            $this->addFlash('success', 'Profil médecin mis à jour');
        }

        return $this->render('profile/profile_medecin.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}





