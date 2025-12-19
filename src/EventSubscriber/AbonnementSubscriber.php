<?php

namespace App\EventSubscriber;

use App\Entity\Medecin;
use App\Service\AbonnementAccessService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class AbonnementSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private AbonnementAccessService $accessService,
        private RouterInterface $router
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest'
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $user = $request->getUser();

        // uniquement pour médecins
        if (!$user instanceof Medecin) {
            return;
        }

        // routes autorisées sans abonnement
        $allowedRoutes = [
            'abonnement_choose',
            'abonnement_subscribe',
            'app_logout'
        ];

        if (!$this->accessService->medecinHasAccess($user)) {
            if (!in_array($request->attributes->get('_route'), $allowedRoutes, true)) {
                $event->setResponse(
                    new RedirectResponse(
                        $this->router->generate('abonnement_choose')
                    )
                );
            }
        }
    }
}
