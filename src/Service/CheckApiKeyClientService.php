<?php
// src/Service/CheckApiKeyClientService.php
namespace App\Service;

use AllowDynamicProperties;
use App\Repository\ClientApiRepository;
use Doctrine\ORM\EntityManagerInterface;

#[AllowDynamicProperties] class CheckApiKeyClientService
{
    private $clientApiRepository;

    public function __construct(ClientApiRepository $clientApiRepository, EntityManagerInterface $entityManager)
    {
        $this->clientApiRepository = $clientApiRepository;
        $this->entityManager = $entityManager;
    }

    public function checkKey(?string $apiKeyClient)
    {
        if (!$apiKeyClient) {
            return null;
        }
        $hashedApiKey = hash('sha256', $apiKeyClient);
        $client = $this->clientApiRepository->findOneBy(['apiKey' => $hashedApiKey]);
        if ($client->getRequestQuota() <= 0) {
            return null;
        }else{
            $client->setRequestQuota($client->getRequestQuota() - 1);
            $this->entityManager->persist($client);
            $this->entityManager->flush();
            return $client;
        }
    }
}
