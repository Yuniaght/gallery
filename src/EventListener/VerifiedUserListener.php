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
    )
    {

    }

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
        $currentRoute = $event->getRequest()->attributes->get('_route');
        if (null === $currentRoute) {
            return;
        }
        $allowedRoutes = [
            'app_registered',
            'app_verify_resend_email',
            'app_verify_email',
            'app_logout',
        ];
        if (in_array($currentRoute, $allowedRoutes)) {
            return;
        }
        $response = new RedirectResponse(
            $this->router->generate('app_registered', ['userName' => $user->getUserName()])
        );
        $event->setResponse($response);
    }
}
