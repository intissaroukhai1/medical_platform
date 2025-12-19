<?php

namespace App\Controller;
use App\Form\AdminProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard()
    {
        return $this->render('admin/dashboard.html.twig');
    }
    #[Route('/admin/profile', name: 'admin_profile')]
public function profile(Request $request, EntityManagerInterface $em)
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
