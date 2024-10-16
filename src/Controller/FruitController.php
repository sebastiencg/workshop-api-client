<?php

namespace App\Controller;

use App\Entity\Fruit;
use App\Form\FruitType;
use App\Repository\ContinentRepository;
use App\Repository\FruitRepository;
use App\Repository\TypeFamilyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/fruit')]
final class FruitController extends AbstractController
{
    #[Route(name: 'app_fruit_index', methods: ['GET'])]
    public function index(FruitRepository $fruitRepository): Response
    {
        return $this->json($fruitRepository->findAll(),Response::HTTP_OK,[],['groups'=>'fruit:show-all']);

    }

    #[Route('/search', name: 'app_fruit_search', methods: ['GET'])]
    public function search(Request $request, FruitRepository $fruitRepository): Response
    {
        $name = $request->query->get('name', '');

        if (empty($name)) {
            return $this->json(['message' => 'Name parameter is required'], Response::HTTP_BAD_REQUEST);
        }

        $fruits = $fruitRepository->findByNameLike($name);

        return $this->json($fruits, Response::HTTP_OK, [], ['groups' => 'fruit:show-all']);
    }

    #[Route('/create/new', name: 'app_fruit_new', methods: ['GET', 'POST'])]
    public function new(SerializerInterface $serializer,Request $request, EntityManagerInterface $entityManager ,ContinentRepository $continentRepository,TypeFamilyRepository $familyRepository ): Response
    {
        $json = $request->getContent();


        if($json===""){
            return $this->json(['message' => 'name must not be empty'], Response::HTTP_BAD_REQUEST);
        }

        $fruit = $serializer->deserialize($json, Fruit::class, 'json');

        $data = json_decode($json, true);
        if (isset($data['continents'])) {
            $continentIds = $data['continents'];
            foreach ($continentIds as $continentId) {
                $continent = $continentRepository->find($continentId);
                if ($continent) {
                    // Ajouter le continent au fruit
                    $fruit->addContinent($continent);
                } else {
                    return $this->json(['message' => "Continent with ID $continentId no find"], Response::HTTP_BAD_REQUEST);
                }
            }
        }

        if (isset($data['families'])) {
        $familyIds = $data['families'];
        foreach ($familyIds as $familyId) {
            $family = $familyRepository->find($familyId);
            if ($family) {
                // Ajouter le continent au fruit
                $fruit->addType($family);
            } else {
                return $this->json(['message' => "family with ID $familyId no find"], Response::HTTP_BAD_REQUEST);
            }
        }
    }

        if ($fruit instanceof Fruit) {
            $fruit->setOfUser($this->getUser());
            $entityManager->persist($fruit);
            $entityManager->flush();

            return $this->json($fruit,Response::HTTP_OK,[],['groups'=>'fruit:show-all']);

        }else{
            return $this->json(['message' => 'the values sent do not match the entity'], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_fruit_show', methods: ['GET'])]
    public function show(Fruit $fruit): Response
    {
        return $this->json($fruit,Response::HTTP_OK,[],['groups'=>'fruit:show-all']);

    }

    #[Route('/{id}/edit', name: 'app_fruit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request,SerializerInterface $serializer, Fruit $fruit, EntityManagerInterface $entityManager): Response
    {
        $json = $request->getContent();
        if($json===""){
            return $this->json(['message' => 'fruit must not be empty'], Response::HTTP_BAD_REQUEST);
        }

        $newFruit = $serializer->deserialize($json, Fruit::class, 'json');

        if ($newFruit instanceof Fruit) {
            if ($fruit->getOfUser() === $this->getUser()) {
                $fruit->setName($newFruit->getName());
                $fruit->setColor($newFruit->getColor());
                $entityManager->persist($fruit);
                $entityManager->flush();

                return $this->json($fruit,Response::HTTP_OK,[],['groups'=>'fruit:show-all']);
            }else{
                return $this->json(['message' => 'no authorization'], Response::HTTP_BAD_REQUEST);
            }



        }else{
            return $this->json(['message' => 'the values sent do not match the entity'], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_fruit_delete', methods: ['POST'])]
    public function delete(Request $request, Fruit $fruit, EntityManagerInterface $entityManager): Response
    {
        if ($fruit->getOfUser() === $this->getUser()) {
            $entityManager->remove($fruit);
            $entityManager->flush();
            return $this->json(['message' => 'values deleted'], Response::HTTP_BAD_REQUEST);
        }else{
            return $this->json(['message' => 'no authorization'], Response::HTTP_BAD_REQUEST);

        }


    }
}
