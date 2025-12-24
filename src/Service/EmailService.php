<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Entity\RendezVous;


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
    public function sendRdvReminder(
    string $to,
    string $prenom,
    RendezVous $rdv
): void {
    $mail = (new Email())
        ->from('no-reply@dawini.tn')
        ->to($to)
        ->subject('🔔 Rappel de votre rendez-vous')
        ->html(sprintf(
            "
            <p>Bonjour <strong>%s</strong>,</p>
            <p>Ceci est un rappel pour votre rendez-vous prévu aujourd’hui à <strong>%s</strong>.</p>
            <p>Cordialement,<br>Cabinet médical</p>
            ",
            $prenom,
            $rdv->getDate()->format('H:i')
        ));

    $this->mailer->send($mail);
}
}
