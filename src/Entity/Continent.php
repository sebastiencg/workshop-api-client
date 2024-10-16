<?php

namespace App\Entity;

use App\Repository\ContinentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ContinentRepository::class)]
class Continent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fruit:show-all','continent:show-all'])]

    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fruit:show-all','continent:show-all'])]

    private ?string $name = null;

    /**
     * @var Collection<int, Fruit>
     */
    #[ORM\ManyToMany(targetEntity: Fruit::class, mappedBy: 'Continent')]
    #[Groups(['continent:show-all'])]
    private Collection $fruit;

    #[ORM\ManyToOne(inversedBy: 'continents')]
    #[Groups(['fruit:show-all','continent:show-all'])]

    private ?User $ofUser = null;

    public function __construct()
    {
        $this->fruit = new ArrayCollection();
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

    /**
     * @return Collection<int, Fruit>
     */
    public function getFruit(): Collection
    {
        return $this->fruit;
    }

    public function addFruit(Fruit $fruit): static
    {
        if (!$this->fruit->contains($fruit)) {
            $this->fruit->add($fruit);
            $fruit->addContinent($this);
        }

        return $this;
    }

    public function removeFruit(Fruit $fruit): static
    {
        if ($this->fruit->removeElement($fruit)) {
            $fruit->removeContinent($this);
        }

        return $this;
    }

    public function getOfUser(): ?User
    {
        return $this->ofUser;
    }

    public function setOfUser(?User $ofUser): static
    {
        $this->ofUser = $ofUser;

        return $this;
    }
}
