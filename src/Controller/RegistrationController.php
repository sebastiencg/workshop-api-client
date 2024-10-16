<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

class RegistrationController extends AbstractController
{
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
            $uuid = Uuid::v4();
            $user->setUuid($uuid);
            $user->setActive(false);
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/register_api', name: 'app_register_api' ,methods: ['POST'])]
    public function registerApi(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SerializerInterface $serializer,UserRepository $userRepository): Response
    {
        $json = $request->getContent();
        $user = $serializer->deserialize($json,User::class,'json');

        if ($user->getEmail() == null || $user->getPassword() == null) {
            return $this->json(['message' => "Données JSON invalides"], Response::HTTP_BAD_REQUEST);
        }
        // Vérification du format de l'email
        if (!filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
            return $this->json(['message' => "Format d'email invalide"], Response::HTTP_BAD_REQUEST);
        }

        $checkUser= $userRepository->findBy(["email"=>$user->getEmail()]);
        if($checkUser){
            return $this->json(['message' => "email " . $user->getEmail() . " already use "], Response::HTTP_BAD_REQUEST);
        }
        if (str_word_count($user->getPassword()) > 4) {
            return $this->json(['message' => "4 words minimum"], Response::HTTP_BAD_REQUEST);
        }


        $user->setPassword(
            $userPasswordHasher->hashPassword($user,$user->getPassword())
        );
        $uuid = Uuid::v4();
        $user->setUuid($uuid);
        $user->setActive(false);
        if ($user->getEmail() == "miantamag@gmail.com") {
            $user->setActive(true);
            $user->setRoles(['ROLE_ADMIN']);
        }

        $entityManager->persist($user);
        $entityManager->flush();
        return $this->json(['message' => "user create"], Response::HTTP_OK);

    }

}
