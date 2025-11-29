<?php

namespace App\Security;

use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class JWTAuthenticator extends AbstractAuthenticator
{
    public function __construct(private UserRepository $userRepo) {}

    public function supports(Request $request): ?bool
    {
        // On active l'auth si le header Authorization existe
        return $request->headers->has('Authorization');
    }

    public function authenticate(Request $request): Passport
    {
        $header = $request->headers->get('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            throw new AuthenticationException('Missing or invalid Authorization header');
        }

        $jwt = substr($header, 7);

        // --- Chargement de la clé publique ---
        $path = realpath(__DIR__ . '/../../config/jwt/public.pem');

        if (!$path || !file_exists($path)) {
            throw new AuthenticationException("Public key not found at: $path");
        }

        $publicKey = file_get_contents($path);

        if (!$publicKey || strlen(trim($publicKey)) < 100) {
            throw new AuthenticationException("Public key is empty or invalid");
        }

        // --- Décodage du token ---
        try {
            $decoded = JWT::decode($jwt, new Key($publicKey, 'RS256'));
        } catch (\Exception $e) {
            throw new AuthenticationException("Invalid token: " . $e->getMessage());
        }

        // --- Utilisateur ---
        if (empty($decoded->email)) {
            throw new AuthenticationException("Token missing email claim");
        }

        return new Passport(
            new UserBadge($decoded->email),
            []
        );
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): ?Response {
        return null; // Continuer l'exécution normale
    }

    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ): ?Response {
        return new JsonResponse([
            'message' => 'Unauthorized',
            'error' => $exception->getMessage(),
        ], 401);
    }
}
