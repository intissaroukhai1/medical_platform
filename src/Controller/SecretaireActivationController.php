<?php

namespace App\Controller;

use App\Repository\SecretaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecretaireActivationController extends AbstractController
{
    #[Route('/secretaire/activate/{token}', name: 'secretaire_activate', methods: ['GET', 'POST'])]
    public function activate(
        string $token,
        Request $request,
        SecretaireRepository $repo,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response {
        $secretaire = $repo->findOneBy(['activationToken' => $token]);

        if (!$secretaire) {
            throw $this->createNotFoundException('Lien invalide ou expiré.');
        }

        if ($request->isMethod('POST')) {
            $password = $request->request->get('password');

            if (strlen($password) < 6) {
                $this->addFlash('danger', 'Mot de passe trop court.');
                return $this->redirectToRoute('secretaire_activate', [
                    'token' => $token
                ]);
            }

            $secretaire->setPassword(
                $hasher->hashPassword($secretaire, $password)
            );
            $secretaire->setActivationToken(null);
            $secretaire->setActif(true);

            $em->flush();

            $this->addFlash('success', 'Compte activé avec succès.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/secretaire_activate.html.twig');
    }
}
