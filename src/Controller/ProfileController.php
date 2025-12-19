<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'user_profile')]
    public function profile(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_profile');
        }

        if ($this->isGranted('ROLE_MEDECIN')) {
            return $this->redirectToRoute('medecin_profil');
        }

        if ($this->isGranted('ROLE_SECRETAIRE')) {
            return $this->redirectToRoute('secretaire_profile');
        }

        throw $this->createAccessDeniedException();
    }
}
