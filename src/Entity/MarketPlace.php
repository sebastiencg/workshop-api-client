<?php

namespace App\Entity;

use App\Repository\MarketPlaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketPlaceRepository::class)]
class MarketPlace
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $apiKey = null;

    /**
     * @var Collection<int, ClientApi>
     */
    #[ORM\OneToMany(targetEntity: ClientApi::class, mappedBy: 'ofMarketPlace')]
    private Collection $clientApis;

    public function __construct()
    {
        $this->clientApis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    /**
     * @return Collection<int, ClientApi>
     */
    public function getClientApis(): Collection
    {
        return $this->clientApis;
    }

    public function addClientApi(ClientApi $clientApi): static
    {
        if (!$this->clientApis->contains($clientApi)) {
            $this->clientApis->add($clientApi);
            $clientApi->setOfMarketPlace($this);
        }

        return $this;
    }

    public function removeClientApi(ClientApi $clientApi): static
    {
        if ($this->clientApis->removeElement($clientApi)) {
            // set the owning side to null (unless already changed)
            if ($clientApi->getOfMarketPlace() === $this) {
                $clientApi->setOfMarketPlace(null);
            }
        }

        return $this;
    }
}
