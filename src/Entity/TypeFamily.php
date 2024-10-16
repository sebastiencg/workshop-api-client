<?php

namespace App\Entity;

use App\Repository\TypeFamilyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TypeFamilyRepository::class)]
class TypeFamily
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fruit:show-all','family:show-all'])]

    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fruit:show-all','family:show-all'])]

    private ?string $name = null;

    /**
     * @var Collection<int, Fruit>
     */
    #[ORM\ManyToMany(targetEntity: Fruit::class, mappedBy: 'type')]
    #[Groups(['family:show-all'])]
    private Collection $fruits;

    #[ORM\ManyToOne(inversedBy: 'typeFamilies')]
    #[Groups(['fruit:show-all','family:show-all'])]

    private ?User $ofUser = null;

    public function __construct()
    {
        $this->fruits = new ArrayCollection();
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
    public function getFruits(): Collection
    {
        return $this->fruits;
    }

    public function addFruit(Fruit $fruit): static
    {
        if (!$this->fruits->contains($fruit)) {
            $this->fruits->add($fruit);
            $fruit->addType($this);
        }

        return $this;
    }

    public function removeFruit(Fruit $fruit): static
    {
        if ($this->fruits->removeElement($fruit)) {
            $fruit->removeType($this);
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
