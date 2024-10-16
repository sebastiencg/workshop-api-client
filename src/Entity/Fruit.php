<?php

namespace App\Entity;

use App\Repository\FruitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: FruitRepository::class)]
class Fruit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fruit:show-all','continent:show-all','family:show-all'])]

    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fruit:show-all','continent:show-all','family:show-all'])]

    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fruit:show-all','continent:show-all','family:show-all'])]

    private ?string $color = null;

    /**
     * @var Collection<int, Continent>
     */
    #[ORM\ManyToMany(targetEntity: Continent::class, inversedBy: 'type')]
    #[Groups(['fruit:show-all'])]

    private Collection $Continent;

    /**
     * @var Collection<int, TypeFamily>
     */
    #[ORM\ManyToMany(targetEntity: TypeFamily::class, inversedBy: 'fruits')]
    #[Groups(['fruit:show-all'])]

    private Collection $type;

    #[ORM\ManyToOne(inversedBy: 'fruits')]
    #[Groups(['fruit:show-all','continent:show-all','family:show-all'])]

    private ?User $ofUser = null;

    public function __construct()
    {
        $this->Continent = new ArrayCollection();
        $this->type = new ArrayCollection();
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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection<int, Continent>
     */
    public function getContinent(): Collection
    {
        return $this->Continent;
    }

    public function addContinent(Continent $continent): static
    {
        if (!$this->Continent->contains($continent)) {
            $this->Continent->add($continent);
        }

        return $this;
    }

    public function removeContinent(Continent $continent): static
    {
        $this->Continent->removeElement($continent);

        return $this;
    }

    /**
     * @return Collection<int, TypeFamily>
     */
    public function getType(): Collection
    {
        return $this->type;
    }

    public function addType(TypeFamily $type): static
    {
        if (!$this->type->contains($type)) {
            $this->type->add($type);
        }

        return $this;
    }

    public function removeType(TypeFamily $type): static
    {
        $this->type->removeElement($type);

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
