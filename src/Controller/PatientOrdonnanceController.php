<?php

namespace App\Controller;

use App\Entity\Ordonnance;
use App\Repository\OrdonnanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_PATIENT')]
#[Route('/patient/ordonnances')]
class PatientOrdonnanceController extends AbstractController
{
    #[Route('', name: 'patient_ordonnances')]
    public function index(OrdonnanceRepository $ordonnanceRepo): Response
    {
        $patient = $this->getUser();

        $ordonnances = $ordonnanceRepo->findBy(
            ['patient' => $patient],
            ['createdAt' => 'DESC']
        );

        return $this->render('patient/ordonnance.html.twig', [
            'ordonnances' => $ordonnances
        ]);
    }
}
