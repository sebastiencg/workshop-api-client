<?php

namespace App\Controller;

use App\Entity\Continent;
use App\Form\ContinentType;
use App\Repository\ContinentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/continent')]
final class ContinentController extends AbstractController
{
    #[Route(name: 'app_continent_index', methods: ['GET'])]
    public function index(ContinentRepository $continentRepository): Response
    {
        return $this->json($continentRepository->findAll(),Response::HTTP_OK,[],['groups'=>'continent:show-all']);
    }

    #[Route('/create/new', name: 'app_continent_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,SerializerInterface $serializer): Response
    {
        $json = $request->getContent();

        if($json===""){
            return $this->json(['message' => 'name must not be empty'], Response::HTTP_BAD_REQUEST);
        }

        $continent = $serializer->deserialize($json, Continent::class, 'json');

        if ($continent instanceof Continent) {
            $continent->setOfUser($this->getUser());

            $entityManager->persist($continent);
            $entityManager->flush();

            return $this->json($continent,Response::HTTP_OK,[],['groups'=>'continent:show-all']);

        }else{
            return $this->json(['message' => 'the values sent do not match the entity'], Response::HTTP_BAD_REQUEST);
        }

    }

    #[Route('/{id}', name: 'app_continent_show', methods: ['GET'])]
    public function show(Continent $continent): Response
    {
        return $this->json($continent,Response::HTTP_OK,[],['groups'=>'continent:show-all']);

    }

    #[Route('/{id}/edit', name: 'app_continent_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request,SerializerInterface $serializer, Continent $continent, EntityManagerInterface $entityManager): Response
    {
        $json = $request->getContent();
        if($json===""){
            return $this->json(['message' => 'name must not be empty'], Response::HTTP_BAD_REQUEST);
        }

        $newContinent = $serializer->deserialize($json, Continent::class, 'json');

        if ($newContinent instanceof Continent) {
            if ($continent->getOfUser() === $this->getUser()) {
                $continent->setName($newContinent->getName());
                $entityManager->persist($continent);
                $entityManager->flush();

                return $this->json($continent,Response::HTTP_OK,[],['groups'=>'continent:show-all']);

            }else{
                return $this->json(['message' => 'no authorization'], Response::HTTP_BAD_REQUEST);

            }


        }else{
            return $this->json(['message' => 'the values sent do not match the entity'], Response::HTTP_BAD_REQUEST);
        }

    }

    #[Route('/{id}', name: 'app_continent_delete', methods: ['DELETE'])]
    public function delete(Request $request, Continent $continent, EntityManagerInterface $entityManager): Response
    {
        if ($continent->getOfUser() === $this->getUser()) {
            $entityManager->remove($continent);
            $entityManager->flush();
            return $this->json(['message' => 'values deleted'], Response::HTTP_BAD_REQUEST);
        }else{
            return $this->json(['message' => 'no authorization'], Response::HTTP_BAD_REQUEST);

        }


    }


}
