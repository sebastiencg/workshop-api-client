<?php

namespace App\Controller;

use App\Entity\ClientApi;
use App\Repository\ClientApiRepository;
use App\Repository\MarketPlaceRepository;
use App\Service\CheckApiKeyMarketPlaceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

class ClientApiController extends AbstractController
{
    #[Route('/admin/client-api', name: 'app_client_api')]
    public function index(ClientApiRepository $clientApiRepository): Response
    {

        return $this->render('client_api/index.html.twig', [
            'clients' => $clientApiRepository->findAll(),
        ]);
    }
    #[Route('/client-api/add', name: 'app_client_api_add',methods: ['POST'])]
    public function add(Request $request, SerializerInterface $serializer ,EntityManagerInterface $entityManager,MarketPlaceRepository $marketPlaceRepository ,CheckApiKeyMarketPlaceService $service): Response
    {
        $apiKeyMarketPlace = $request->headers->get('API-Key-Plat');

        $marketPlace = $service->checkKey($apiKeyMarketPlace);
        if (!$marketPlace){
            return $this->json(['message' => "MarketPlace unknown"], Response::HTTP_BAD_REQUEST);
        }

        $json = $request->getContent();
        $data = json_decode($json, true);

        if (!isset($data['API_key'])) {
            return $this->json(['message' => "API_key no send"], Response::HTTP_BAD_REQUEST);
        }
        if (!isset($data['email'])) {
            return $this->json(['message' => "email no send"], Response::HTTP_BAD_REQUEST);
        }
        if (!isset($data['total_request'])) {
            return $this->json(['message' => "total request  no send"], Response::HTTP_BAD_REQUEST);
        }
        if (!isset($data['uuid'])) {
            return $this->json(['message' => "uuid no send"], Response::HTTP_BAD_REQUEST);
        }
        $checkUuid= Uuid::isValid($data['uuid']);
        if (!$checkUuid) {
            return $this->json(['message' => "uuid invalid"], Response::HTTP_BAD_REQUEST);
        }
        $uuid = Uuid::fromString($data['uuid']);

        $client = new ClientApi();
        $client->setEmail($data['email']);
        $client->setApiKey($data['API_key']);
        $client->setTotalRequest($data['total_request']);
        $client->setRequestQuota($client->getTotalRequest());
        $client->setUuid($uuid);
        $client->setOfMarketPlace($marketPlace);

        $entityManager->persist($client);
        $entityManager->flush();
        return $this->json(['message' => "user create"], Response::HTTP_OK);

    }

    #[Route('/client-api/revoke', name: 'app_client_api_revoke',methods: ['DELETE'])]
    public function revoke(ClientApiRepository $clientApiRepository,Request $request,EntityManagerInterface $entityManager,CheckApiKeyMarketPlaceService $service ): Response
    {


        $apiKeyMarketPlace = $request->headers->get('API-Key-Plat');
        if(!$apiKeyMarketPlace){
            return $this->json(['message' => "MarketPlace unknown"], Response::HTTP_BAD_REQUEST);
        }else{
            $marketPlace = $service->checkKey($apiKeyMarketPlace);
            if(!$marketPlace){
                return $this->json(['message' => "MarketPlace unknown"], Response::HTTP_BAD_REQUEST);
            }
            $uuidQuery = $request->query->get('uuid');

            if (!$uuidQuery) {
                return $this->json(['message' => "uuid no send"], Response::HTTP_BAD_REQUEST);
            }

            $checkUuid= Uuid::isValid($uuidQuery);

            if (!$checkUuid) {
                return $this->json(['message' => "uuid invalid"], Response::HTTP_BAD_REQUEST);
            }

            $uuid = Uuid::fromString($uuidQuery);

            $clientApi = $clientApiRepository->findOneBy(['Uuid'=>$uuid,]);

            $entityManager->remove($clientApi);
            $entityManager->flush();

            return $this->json(['message' => "uuid revoked"], Response::HTTP_OK);

        }
    }

    #[Route('/client-api/info', name: 'app_client_api_info',methods: ['GET'])]
    public function info(ClientApiRepository $clientApiRepository,Request $request,MarketPlaceRepository $marketPlaceRepository ,EntityManagerInterface $entityManager,CheckApiKeyMarketPlaceService $service): Response
    {
        $apiKeyMarketPlace = $request->headers->get('API-Key-Plat');
        if(!$apiKeyMarketPlace){
            return $this->json(['message' => "MarketPlace unknown"], Response::HTTP_BAD_REQUEST);
        }else{
            $marketPlace = $service->checkKey($apiKeyMarketPlace);
            if(!$marketPlace){
                return $this->json(['message' => "MarketPlace unknown"], Response::HTTP_BAD_REQUEST);
            }
            $uuidQuery = $request->query->get('uuid');

            if (!$uuidQuery) {
                return $this->json(['message' => "uuid no send"], Response::HTTP_BAD_REQUEST);
            }

            $checkUuid= Uuid::isValid($uuidQuery);

            if (!$checkUuid) {
                return $this->json(['message' => "uuid invalid"], Response::HTTP_BAD_REQUEST);
            }

            $uuid = Uuid::fromString($uuidQuery);

            $clientApi = $clientApiRepository->findOneBy(['Uuid'=>$uuid,]);

            return $this->json($clientApi, Response::HTTP_OK, [], ['groups' => 'marketPlace:show-client']);


    }
    }
}
