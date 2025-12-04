<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request): Response
    {
        $routeParams = ['login' => 'true'];
        $session = $request->getSession();
        $targetPath = $session->get('_security.main.target_path');
        if ($targetPath) {
            $routeParams['access_denied'] = 'true';
        }
        return $this->redirectToRoute('app_home', $routeParams);
    }

    public function loginModal(AuthenticationUtils $authenticationUtils, mixed $checkAccessDenied = null): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('partials/loginModal.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'access_denied' => $checkAccessDenied
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
