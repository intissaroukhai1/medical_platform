<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    public function __construct(private MailerInterface $mailer) {}

    public function sendSecretaireInvitation(
        string $email,
        string $prenom,
        string $token
    ): void {
        $mail = (new Email())
            ->from('no-reply@dawini.tn')   // ✅ OK (Mailtrap accepte)
            ->to($email)                  // ✅ OK
            ->subject('Création de votre compte Secrétaire') // ✅ OK
            ->html("                      // ✅ HTML OK
                <h2>Bienvenue $prenom 👋</h2>
                <p>Un médecin vous a ajouté comme secrétaire.</p>
                <p>
                    <a href='http://localhost:8000/secretaire/activate/$token'>
                    Créer mon mot de passe
                    </a>
                </p>
                <p>Ce lien est valide 24h.</p>
            ");

        $this->mailer->send($mail);        // ✅ ENVOI OK
    }
}
