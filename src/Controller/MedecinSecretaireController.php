<?php

namespace App\Controller;

use App\Entity\Secretaire;
use App\Repository\SecretaireRepository;
use App\Service\AbonnementAccessService;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[IsGranted('ROLE_MEDECIN')]
#[Route('/medecin/secretaires')]
class MedecinSecretaireController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private AbonnementAccessService $accessService,
        private EmailService $emailService
    ) {}

  #[Route('', name: 'medecin_secretaires')]
public function index(): Response
{
    /** @var \App\Entity\Medecin $medecin */
    $medecin = $this->getUser();

    return $this->render('medecin/secretaire.html.twig', [
        'medecin'     => $medecin,
        'secretaires' => $medecin->getSecretaires(),
    ]);
}


    #[Route('/add', name: 'medecin_secretaire_add', methods: ['POST'])]
    public function addSecretaire(Request $request): RedirectResponse
    {
        $medecin = $this->getUser();

        if (!$this->accessService->canAddSecretaire($medecin)) {
            $this->addFlash('danger', 'Quota atteint ou abonnement inactif.');
            return $this->redirectToRoute('medecin_secretaires');
        }

        $email  = $request->request->get('email');
        $nom    = $request->request->get('nom');
        $prenom = $request->request->get('prenom');
        $typeContrat = $request->request->get('typeContrat');

        if (!$email || !$nom || !$prenom) {
            $this->addFlash('danger', 'Tous les champs sont obligatoires.');
            return $this->redirectToRoute('medecin_secretaires');
        }

        $secretaire = $this->em->getRepository(Secretaire::class)
            ->findOneBy(['email' => $email]);

        if ($secretaire && $secretaire->getMedecin() !== null) {
            $this->addFlash('danger', 'Cette secrétaire est déjà liée.');
            return $this->redirectToRoute('medecin_secretaires');
        }

        if (!$secretaire) {
            $secretaire = new Secretaire();
            $secretaire->setEmail($email);
            $secretaire->setNom($nom);
            $secretaire->setPrenom($prenom);
            $secretaire->setTypeContrat($typeContrat);
            $secretaire->setRoles(['ROLE_SECRETAIRE']);
            $secretaire->setActif(false);

            $token = Uuid::v4()->toRfc4122();
            $secretaire->setActivationToken($token);
            $secretaire->setPassword('TEMP'); // champ non nullable

            $this->em->persist($secretaire);
        } else {
            $token = $secretaire->getActivationToken();
        }

        $secretaire->setMedecin($medecin);
        $this->em->flush();

        // 📧 Email après flush
        $this->emailService->sendSecretaireInvitation(
            $email,
            $prenom,
            $token
        );
      


        $this->addFlash('success', 'Secrétaire ajoutée, email envoyé.');
        return $this->redirectToRoute('medecin_secretaires');
    }

   
    #[Route('/{id}/toggle', name: 'medecin_secretaire_toggle', methods: ['POST'])]
public function toggleSecretaire(Secretaire $secretaire): RedirectResponse
{
    /** @var \App\Entity\Medecin $medecin */
    $medecin = $this->getUser();

    // Sécurité : la secrétaire doit appartenir au médecin
    if ($secretaire->getMedecin() !== $medecin) {
        throw $this->createAccessDeniedException();
    }

    $secretaire->setActif(!$secretaire->isActif());
    $this->em->flush();

    return $this->redirectToRoute('medecin_secretaires');
}
#[Route('/{id}/remove', name: 'medecin_secretaire_remove', methods: ['POST'])]
public function removeSecretaire(Secretaire $secretaire): RedirectResponse
{
    /** @var \App\Entity\Medecin $medecin */
    $medecin = $this->getUser();

    // Sécurité : la secrétaire doit appartenir au médecin
    if ($secretaire->getMedecin() !== $medecin) {
        throw $this->createAccessDeniedException();
    }

    $this->em->remove($secretaire);
    $this->em->flush();

    return $this->redirectToRoute('medecin_secretaires');
}

}
