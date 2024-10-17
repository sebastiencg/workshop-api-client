<?php
// src/Service/CheckApiKeyClientService.php
namespace App\Service;

use App\Repository\ClientApiRepository;

class CheckApiKeyClientService
{
    private $clientApiRepository;

    public function __construct(ClientApiRepository $clientApiRepository)
    {
        $this->clientApiRepository = $clientApiRepository;
    }

    public function checkKey(?string $apiKeyClient)
    {
        if (!$apiKeyClient) {
            return null;
        }
        $hashedApiKey = hash('sha256', $apiKeyClient);
        $client = $this->clientApiRepository->findOneBy(['apiKey' => $hashedApiKey]);
        return $client ?: null;
    }
}
