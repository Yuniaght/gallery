<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setRoles(['ROLE_USER']);
            $user->setJoinedAt(new \DateTimeImmutable('now'))
                 ->setEditedAt(new \DateTimeImmutable('now'))
                 ->setImage('default.jpg')
                 ->setIsActive(1)
                 ->setIsVerified(0);
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@gallery.com', 'Gallery'))
                    ->to((string) $user->getEmail())
                    ->subject('Confirmer votre compte')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_registered', [
                'userName' => $user->getUserName()
            ]);
        }

        return $this->render('pages/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $user): Response
    {
        $userName = $request->query->get('userName');
        if (null === $userName) {
            return $this->redirectToRoute('app_register');
        }
        $user = $user->findOneBy(['userName' => $userName]);
        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        return $this->redirectToRoute('app_verified');
    }

    #[Route('/registered/{userName}', name: 'app_registered')]
    public function registered(string $userName): Response
    {
        return $this->render('pages/registered.html.twig', [
            'userName' => $userName
        ]);
    }

    #[Route('/verified', name: 'app_verified')]
    public function verified(): Response
    {
        return $this->render('pages/verified.html.twig');
    }

    #[Route('/verify/resend/{userName}', name: 'app_verify_resend_email')]
    public function resendVerifyEmail(string $userName, UserRepository $user): Response
    {
        $user = $user->findOneBy(
            ['userName' => $userName]
        );

        if (!$user) {
            $this->addFlash('danger', 'Utilisateur introuvable.');
            return $this->redirectToRoute('app_register');
        }
        if ($user->isVerified()) {
            $this->addFlash('info', 'Ce compte est déjà vérifié. Vous pouvez vous connecter.');
            return $this->redirectToRoute('app_home');
        }
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('no-reply@gallery.com', 'Gallery'))
                ->to((string) $user->getEmail())
                ->subject('Nouveau lien de confirmation')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
        $this->addFlash('success', 'Un nouveau lien a été envoyé à ' . $user->getEmail());
        return $this->redirectToRoute('app_registered', ['userName' => $userName]);
    }
}
