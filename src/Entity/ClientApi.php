<?php

namespace App\Entity;

use App\Repository\ClientApiRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ClientApiRepository::class)]
class ClientApi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['marketPlace:show-client'])]

    private ?string $email = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $apiKey = null;

    #[ORM\Column(type: 'uuid')]
    #[Groups(['marketPlace:show-client'])]

    private ?Uuid $Uuid = null;

    #[ORM\Column]
    #[Groups(['marketPlace:show-client'])]
    private ?int $totalRequest = null;

    #[ORM\Column]
    #[Groups(['marketPlace:show-client'])]
    private ?int $requestQuota = null;

    #[ORM\ManyToOne(inversedBy: 'clientApis')]
    private ?MarketPlace $ofMarketPlace = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): static
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function getUuid(): ?Uuid
    {
        return $this->Uuid;
    }

    public function setUuid(Uuid $Uuid): static
    {
        $this->Uuid = $Uuid;

        return $this;
    }

    public function getTotalRequest(): ?int
    {
        return $this->totalRequest;
    }

    public function setTotalRequest(int $totalRequest): static
    {
        $this->totalRequest = $totalRequest;

        return $this;
    }

    public function getRequestQuota(): ?int
    {
        return $this->requestQuota;
    }

    public function setRequestQuota(int $requestQuota): static
    {
        $this->requestQuota = $requestQuota;

        return $this;
    }

    public function getOfMarketPlace(): ?MarketPlace
    {
        return $this->ofMarketPlace;
    }

    public function setOfMarketPlace(?MarketPlace $ofMarketPlace): static
    {
        $this->ofMarketPlace = $ofMarketPlace;

        return $this;
    }
}
