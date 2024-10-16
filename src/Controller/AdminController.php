<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/users', name: 'admin_user_list')]
    public function list(UserRepository $userRepository): Response
    {
        // Vérifie que l'utilisateur connecté a le rôle ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $userRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/user/{id}/toggle', name: 'admin_user_toggle')]

    public function toggle(User $user, EntityManagerInterface $entityManager): Response
    {
        // Vérifie que l'utilisateur connecté a le rôle ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Inverse le statut actif de l'utilisateur
        $user->setActive(!$user->isActive());
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin_user_list');
    }
}
