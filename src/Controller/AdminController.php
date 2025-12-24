<?php

namespace App\Controller;
use App\Form\AdminProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]


class AdminController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {// 🔢 Utilisateurs totaux
    $usersCount = $em->getRepository(\App\Entity\User::class)->count([]);

  $medecinsCount = $em->getRepository(\App\Entity\Medecin::class)->count([]);

    // 📅 Consultations (RDV confirmés)
    $consultationsCount = $em->createQuery(
        "SELECT COUNT(r.id) FROM App\Entity\RendezVous r WHERE r.statut = 'CONFIRME'"
    )->getSingleScalarResult();

    // 💰 Revenus (abonnements actifs)
    $revenus = $em->createQuery(
         "
    SELECT COALESCE(SUM(a.prix), 0)
    FROM App\Entity\MedecinAbonnement ma
    JOIN ma.abonnement a
    WHERE ma.statut = :statut
    "
)
->setParameter('statut', \App\Entity\MedecinAbonnement::STATUT_ACTIF)
    ->getSingleScalarResult();
        return $this->render('admin/dashboard.html.twig', [
             'usersCount'         => $usersCount,
        'medecinsCount'      => $medecinsCount,
        'consultationsCount' => $consultationsCount,
        'revenus'            => $revenus ?? 0,
    ]);
    }
    
    
    
    
    #[Route('/admin/profile', name: 'admin_profile')]
public function profile(Request $request, EntityManagerInterface $em): Response
{
    $admin = $this->getUser();

    $form = $this->createForm(AdminProfileType::class, $admin);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $photo = $form->get('photoProfil')->getData();

        if ($photo) {
            $filename = uniqid().'.'.$photo->guessExtension();

            $photo->move(
                $this->getParameter('photo_directory'),
                $filename
            );

            $admin->setPhotoProfil($filename);
        }

        $em->flush();

        $this->addFlash('success', 'Profil mis à jour');
        return $this->redirectToRoute('admin_dashboard');
    }

    return $this->render('profile/profile_admin.html.twig', [
        'form' => $form->createView(),
    ]);
}
}
