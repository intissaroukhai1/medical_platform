<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/utilisateurs')]
class AdminUserController extends AbstractController
{
    #[Route('', name: 'admin_utilisateurs', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/users/index.html.twig', [
            'utilisateurs' => $userRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/create', name: 'admin_utilisateur_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        UserRepository $userRepository,
        UserPasswordHasherInterface $hasher
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid('utilisateur-create', (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'CSRF invalide.');
            return $this->redirectToRoute('admin_utilisateurs');
        }

        $email = trim((string) $request->request->get('email'));
        if ($userRepository->findOneBy(['email' => $email])) {
            $this->addFlash('error', 'Email déjà utilisé.');
            return $this->redirectToRoute('admin_utilisateurs');
        }

        $user = new User();
        $user->setNom((string) $request->request->get('nom'));
        $user->setPrenom((string) $request->request->get('prenom'));
        $user->setEmail($email);
        $user->setTelephone($request->request->get('telephone'));

        $role = (string) $request->request->get('role', 'ROLE_USER');
        $user->setRoles([$role]);

    
        $plainPassword = (string) $request->request->get('password');
        $user->setPassword($hasher->hashPassword($user, $plainPassword));

        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Utilisateur créé.');
        return $this->redirectToRoute('admin_utilisateurs');
    }

    #[Route('/{id}/update', name: 'admin_utilisateur_update', methods: ['POST'])]
    public function update(
        int $id,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid('utilisateur-edit', (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'CSRF invalide.');
            return $this->redirectToRoute('admin_utilisateurs');
        }

        $user = $userRepository->find($id);
        if (!$user) {
            $this->addFlash('error', 'Utilisateur introuvable.');
            return $this->redirectToRoute('admin_utilisateurs');
        }

        $newEmail = trim((string) $request->request->get('email'));
        $existing = $userRepository->findOneBy(['email' => $newEmail]);
        if ($existing && $existing->getId() !== $user->getId()) {
            $this->addFlash('error', 'Email déjà utilisé.');
            return $this->redirectToRoute('admin_utilisateurs');
        }

        $user->setNom((string) $request->request->get('nom'));
        $user->setPrenom((string) $request->request->get('prenom'));
        $user->setEmail($newEmail);
        $user->setTelephone($request->request->get('telephone'));

        $role = (string) $request->request->get('role', 'ROLE_USER');
        $user->setRoles([$role]);


        // si l’admin a rempli un nouveau mot de passe -> update
        $newPassword = trim((string) $request->request->get('password'));
        if ($newPassword !== '') {
            $user->setPassword($hasher->hashPassword($user, $newPassword));
        }

        $em->flush();

        $this->addFlash('success', 'Utilisateur modifié.');
        return $this->redirectToRoute('admin_utilisateurs');
    }

    #[Route('/{id}/delete', name: 'admin_utilisateur_delete', methods: ['POST'])]
public function delete(
    int $id,
    Request $request,
    UserRepository $userRepository,
    EntityManagerInterface $em
): Response {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

   if (!$this->isCsrfTokenValid(
    'utilisateur-delete' . $id,
    (string) $request->request->get('_token')
)) {
    $this->addFlash('error', 'CSRF invalide.');
    return $this->redirectToRoute('admin_utilisateurs');
}


    $user = $userRepository->find($id);
    if (!$user) {
        $this->addFlash('error', 'Utilisateur introuvable.');
        return $this->redirectToRoute('admin_utilisateurs');
    }

    if ($user === $this->getUser()) {
        $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        return $this->redirectToRoute('admin_utilisateurs');
    }

    $em->remove($user);
    $em->flush();

    $this->addFlash('success', 'Utilisateur supprimé.');
    return $this->redirectToRoute('admin_utilisateurs');
}

}
