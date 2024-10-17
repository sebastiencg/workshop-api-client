<?php

namespace App\Service;

use AllowDynamicProperties;
use App\Repository\MarketPlaceRepository;
use Symfony\Component\HttpFoundation\Response;

#[AllowDynamicProperties] class CheckApiKeyMarketPlaceService
{
    public function __construct(MarketPlaceRepository $marketPlaceRepository)
    {
        $this->marketRepo = $marketPlaceRepository;
    }
    public function checkKey(string $apiKeyMarketPlace){
        if(!$apiKeyMarketPlace){
            return null ;
        }else{
            $hashedApiKey = hash('sha256', $apiKeyMarketPlace);
            $marketPlace = $this->marketRepo->findOneBy(['apiKey' => $hashedApiKey]);
            if(!$marketPlace){
                return null ;
            }else{
                return $marketPlace;
            }
        }
    }
}