<?php
// src/Service/ContactFormHandler.php

namespace App\Service;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class ContactFormHandler
{
    private $mailer;
    private $twig;
    private $requestStack;

    public function __construct(MailerInterface $mailer, Environment $twig, EntityManagerInterface $entityManager)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->entityManager = $entityManager;
    }

    public function handle(FormInterface $form): bool
    {
      

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

             //1. Enregistrement des données dans la base de données
            $contact = new Contact();
            $contact = new Contact();
            $contact->setName($data['name']);
            $contact->setFirstName($data['firstName']);
            $contact->setPhone($data['phone']);
            $contact->setEmail($data['email']);
            $contact->setMessage($data['message']);
            $contact->setCreatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($contact);
            $this->entityManager->flush();  

            // 2. Envoi de l'email
            $email = (new Email())
                ->from($data['email'])
                ->to('david.pautrat@gmail.com')
                ->subject('Demande Webmaster Freelance')
                ->html($this->twig->render('emails/contact.html.twig', [
                    'data' => $data,
                ]));

            $this->mailer->send($email);

        
            // Vous pouvez ajouter d'autres actions ici (ex: persister en base de données)

            return true; // Le formulaire a été traité avec succès
        }
        
        if (!$form->isSubmitted() && !$form->isValid()) {
        return false; // Le formulaire n'a pas été soumis ou est invalide
        }
    }
}
?>