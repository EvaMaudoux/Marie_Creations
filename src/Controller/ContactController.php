<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function sendEmail(MailerInterface $mailer, Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $email = (new TemplatedEmail())
                    -> from($contact->getEmail())
                    -> to('marie_dumont@gmail.com')
                    -> subject($contact->getSubject())
                -> htmlTemplate('contact/templating-contact.html.twig')
                -> context([
                    'firstName' => $contact->getFirstName(),
                    'lastName' => $contact->getLastName(),
                    'message' => $contact->getMessage(),
                    'title' => $contact->getSubject(),
                ]);
            $mailer->send($email);

            return $this->redirectToRoute('app_home');
        }

        return $this->render('contact/contact.html.twig', [
            'contactForm' => $form,
        ]);
    }
}
