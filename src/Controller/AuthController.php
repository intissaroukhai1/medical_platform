<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Patient;
use App\Entity\Medecin;
use App\Entity\Secretaire;
use App\Entity\Admin;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Firebase\JWT\JWT;

class AuthController extends AbstractController
{
    #[Route('/api/auth/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['role'])) {
            return new JsonResponse(['message' => 'Invalid JSON'], 400);
        }

        // =============================================
        // 🔥 1) Créer le bon type d'utilisateur
        // =============================================
        $role = strtoupper($data['role']);
        if ($role === 'SECRETAIRE') {
    return new JsonResponse([
        'message' => 'Création de compte secrétaire interdite via inscription publique'
    ], 403);
}

        $entity = null;

        if ($role === "PATIENT")      $entity = new Patient();
        elseif ($role === "MEDECIN")  $entity = new Medecin();
        elseif ($role === "ADMIN")    $entity = new Admin();
        else                           $entity = new User();

        // =============================================
        // 🔥 2) Remplir les champs communs (User)
        // =============================================
        $entity->setEmail($data['email']);
        $entity->setPrenom($data['prenom']);
        $entity->setNom($data['nom']);
        $entity->setRoles(["ROLE_" . $role]);

        $hashed = $passwordHasher->hashPassword($entity, $data['password']);
        $entity->setPassword($hashed);

        // =============================================
        // 🔥 3) Champs spécifiques selon le rôle
        // =============================================
        if ($entity instanceof Patient) {
            $entity->setNumeroCIN($data['numeroCIN']);
            $entity->setGenre($data['genre']);
            $entity->setAdresse($data['adresse']);
            $entity->setMutuelle($data['mutuelle'] ?? null);
            $entity->setGroupeSanguin($data['groupeSanguin']);
        }

        if ($entity instanceof Medecin) {
            $entity->setNumeroOrdre($data['numeroOrdre']);
            $entity->setAdresseCabinet($data['adresseCabinet']);
            $entity->setLatitude($data['latitude'] ?? null);
            $entity->setLongitude($data['longitude'] ?? null);
            $entity->setVille($data['ville']);
            $entity->setCodePostal($data['codePostal']);
            $entity->setDisponibleUrgence(isset($data['disponibleUrgence']));
            $entity->setPrixConsultation($data['prixConsultation']);
            $entity->setExperienceAnnees($data['experienceAnnees']);
            $entity->setBiographie($data['biographie']);
        }


        if ($entity instanceof Admin) {
            $entity->setAccesTotal($data['accesTotal'] ?? false);
        }

        // =============================================
        // 🔥 4) Sauvegarde (UN SEUL INSERT)
        // =============================================
        $em->persist($entity);
        $em->flush();

        // =============================================
        // 🔥 5) Générer JWT
        // =============================================
        $privateKey = file_get_contents(__DIR__ . '/../../config/jwt/private.pem');

        $payload = [
            "id" => $entity->getId(),
            "email" => $entity->getEmail(),
            "role" => $entity->getRoles()[0],
            "exp" => time() + 3600
        ];

        $token = JWT::encode($payload, $privateKey, 'RS256');

        return new JsonResponse([
            "message" => "Inscription réussie",
            "token" => $token,
            "user" => [
                "id" => $entity->getId(),
                "email" => $entity->getEmail(),
                "nom" => $entity->getNom(),
                "prenom" => $entity->getPrenom(),
                "role" => $entity->getRoles()[0]
            ]
        ]);
    }
    #[Route('/register', name: 'app_register', methods: ['GET'])]
public function registerPage(): Response
{
    return $this->render('security/register.html.twig');
}
#[Route('/api/auth/login', name: 'api_login', methods: ['POST'])]
public function apiLogin(
    Request $request,
    UserRepository $userRepo,
    UserPasswordHasherInterface $passwordHasher
): JsonResponse {

    $data = json_decode($request->getContent(), true);

    if (!$data || !isset($data['email'], $data['password'])) {
        return new JsonResponse(["message" => "Email et mot de passe requis"], 400);
    }

    $user = $userRepo->findOneBy(['email' => $data['email']]);

    if (!$user) {
        return new JsonResponse(["message" => "Utilisateur non trouvé"], 404);
    }

    if (!$passwordHasher->isPasswordValid($user, $data['password'])) {
        return new JsonResponse(["message" => "Mot de passe incorrect"], 401);
    }

    // Génération du token
    $privateKey = file_get_contents(__DIR__ . '/../../config/jwt/private.pem');

    $payload = [
        "id" => $user->getId(),
        "email" => $user->getEmail(),
        "role" => $user->getRoles()[0],
        "exp" => time() + 3600
    ];

    $token = JWT::encode($payload, $privateKey, 'RS256');

    return new JsonResponse([
        "message" => "Connexion réussie",
        "token" => $token,
        "user" => [
            "id" => $user->getId(),
            "email" => $user->getEmail(),
            "nom" => $user->getNom(),
            "prenom" => $user->getPrenom(),
            "role" => $user->getRoles()[0]
        ]
    ], 200);
}

#[Route('/api/test', methods: ['GET'])]
public function test(): JsonResponse
{
    return new JsonResponse([
        "message" => "Tu es authentifié !"
    ]);
}
#[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
public function loginPage(AuthenticationUtils $authenticationUtils): Response
{
    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('security/login.html.twig', [
        'last_username' => $lastUsername,
        'error' => $error,
    ]);
}
#[Route('/logout', name: 'app_logout')]
public function logout() {}
}