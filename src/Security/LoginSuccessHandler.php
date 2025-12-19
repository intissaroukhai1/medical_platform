<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $roles = $token->getRoleNames();

        // IMPORTANT : tester l’ordre du rôle le plus haut → au plus bas
        if (in_array('ROLE_ADMIN', $roles)) {
            return new RedirectResponse($this->urlGenerator->generate('admin_dashboard'));
        }

        if (in_array('ROLE_MEDECIN', $roles)) {
            return new RedirectResponse($this->urlGenerator->generate('medecin_dashboard'));
        }

        if (in_array('ROLE_SECRETAIRE', $roles)) {
            return new RedirectResponse($this->urlGenerator->generate('secretaire_dashboard'));
        }

        if (in_array('ROLE_PATIENT', $roles)) {
            return new RedirectResponse($this->urlGenerator->generate('patient_dashboard'));
        }

        // fallback
        return new RedirectResponse('/');
    }
}
