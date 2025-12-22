<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class  EmailService
{
    public function __construct(private MailerInterface $mailer) {}

    public function sendSecretaireInvitation(string $email, string $token): void
    {
        $mail = (new Email())
            ->from('no-reply@dawini.tn')
            ->to($email)
            ->subject('Création de votre compte Secrétaire')
            ->html("
                <h2>Bienvenue sur Dawini</h2>
                <p>Un médecin vous a ajouté comme secrétaire.</p>
                <p>👉 <a href='https://localhost:8000/register/secretaire/$token'>
                    Créer mon mot de passe
                </a></p>
                <p>Ce lien est valide 24h.</p>
            ");

        $this->mailer->send($mail);
    }
}
