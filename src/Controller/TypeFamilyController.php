<?php

namespace App\Controller;

use App\Attribute\RequireApiKey;
use App\Entity\TypeFamily;
use App\Form\TypeFamilyType;
use App\Repository\TypeFamilyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[RequireApiKey]
#[Route('/api/family')]
final class TypeFamilyController extends AbstractController
{
    #[Route(name: 'app_type_family_index', methods: ['GET'])]
    public function index(TypeFamilyRepository $typeFamilyRepository): Response
    {
        return $this->json($typeFamilyRepository->findAll(),Response::HTTP_OK,[],['groups'=>'family:show-all']);

    }

    #[Route('/create/new', name: 'app_type_family_new', methods: ['GET', 'POST'])]
    public function new(SerializerInterface $serializer, Request $request, EntityManagerInterface $entityManager): Response
    {

        $json = $request->getContent();

        if($json===""){
            return $this->json(['message' => 'name must not be empty'], Response::HTTP_BAD_REQUEST);
        }

        $typeFamily = $serializer->deserialize($json, TypeFamily::class, 'json');

        if ($typeFamily instanceof TypeFamily) {
            $typeFamily->setOfUser($this->getUser());

            $entityManager->persist($typeFamily);
            $entityManager->flush();

            return $this->json($typeFamily,Response::HTTP_OK,[],['groups'=>'family:show-all']);

        }else{
            return $this->json(['message' => 'the values sent do not match the entity'], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_type_family_show', methods: ['GET'])]
    public function show(TypeFamily $typeFamily): Response
    {
        return $this->json($typeFamily,Response::HTTP_OK,[],['groups'=>'family:show-all']);

    }

    #[Route('/{id}/edit', name: 'app_type_family_edit', methods: ['GET', 'POST'])]
    public function edit(SerializerInterface $serializer,Request $request, TypeFamily $typeFamily, EntityManagerInterface $entityManager): Response
    {
        $json = $request->getContent();
        if($json===""){
            return $this->json(['message' => 'name must not be empty'], Response::HTTP_BAD_REQUEST);
        }

        $newTypeFamily = $serializer->deserialize($json, TypeFamily::class, 'json');
        if ($typeFamily->getOfUser() === $this->getUser()) {
            if ($newTypeFamily instanceof TypeFamily) {

                $typeFamily->setName($newTypeFamily->getName());
                $entityManager->persist($typeFamily);
                $entityManager->flush();

                return $this->json($typeFamily,Response::HTTP_OK,[],['groups'=>'family:show-all']);

            }else{
                return $this->json(['message' => 'the values sent do not match the entity'], Response::HTTP_BAD_REQUEST);
            }
        }
        return $this->json(['message' => 'internal error'], Response::HTTP_BAD_REQUEST);


    }

    #[Route('/{id}', name: 'app_type_family_delete', methods: ['POST'])]
    public function delete(Request $request, TypeFamily $typeFamily, EntityManagerInterface $entityManager): Response
    {
        if ($typeFamily->getOfUser() === $this->getUser()) {
            $entityManager->remove($typeFamily);
            $entityManager->flush();
            return $this->json(['message' => 'values deleted'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['message' => 'error'], Response::HTTP_BAD_REQUEST);

    }
}
