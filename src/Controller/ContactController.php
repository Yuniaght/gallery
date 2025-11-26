<?php

namespace App\Controller;

use App\Class\Contact;
use App\Form\ContactType;
use App\Repository\WorkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function contact(MailerInterface $mailer, Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $email = (new Email())
                ->from($contact->getEmail())
                ->to('admin@gallery.com')
                ->addCc($contact->getEmail())
                ->subject($contact->getSubject())
                ->text($contact->getMessage());
            $mailer->send($email);
            return $this->redirectToRoute('app_thankyou');
        }
        return $this->render('pages/contact.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('/thankyou', name: 'app_thankyou')]
    public function thanks(MailerInterface $mailer, Request $request): Response
    {
        return $this->render('pages/thankyou.html.twig');
    }
}
