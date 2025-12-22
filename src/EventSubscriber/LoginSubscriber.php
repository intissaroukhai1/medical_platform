<?php

namespace App\EventSubscriber;

use App\Entity\Medecin;
use App\Entity\Secretaire;
use App\Service\AbonnementAccessService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class LoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private AbonnementAccessService $accessService
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [LoginSuccessEvent::class => 'onLoginSuccess'];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        if ($user instanceof Medecin) {
            if (!$this->accessService->medecinHasAccess($user)) {
                throw new AccessDeniedHttpException('Abonnement requis');
            }
        }
if ($user instanceof Secretaire) {
    if (!$this->accessService->secretaireHasAccess($user)) {
        throw new AccessDeniedHttpException('Accès secrétaire désactivé');
    }
}

    }
}
