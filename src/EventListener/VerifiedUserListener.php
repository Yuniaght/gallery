<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

final class VerifiedUserListener
{
    public function __construct(
        private Security $security,
        private RouterInterface $router
    ) {}

    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        if ($user->isVerified()) {
            return;
        }

        // 4. Liste des routes autorisées pour les non-vérifiés
        // Il faut récupérer la route courante
        $currentRoute = $event->getRequest()->attributes->get('_route');

        // Sécurité si la route est nulle (ex: 404)
        if (null === $currentRoute) {
            return;
        }

        $allowedRoutes = [
            'app_registered',           // La page d'attente
            'app_verify_resend_email',  // L'action pour renvoyer le mail
            'app_verify_email',         // Le lien de validation dans le mail
            'app_logout',               // La déconnexion
        ];

        // Si on est sur une route autorisée, on laisse passer
        if (in_array($currentRoute, $allowedRoutes)) {
            return;
        }

        // 5. Redirection forcée vers la page "Merci de vous être inscrit"
        // On passe l'ID pour que le lien "Renvoyer" fonctionne
        $response = new RedirectResponse(
            $this->router->generate('app_registered', ['userName' => $user->getUserName()])
        );

        $event->setResponse($response);
    }
}
