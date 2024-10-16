<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['fruit:show-all','continent:show-all','family:show-all'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['fruit:show-all','continent:show-all','family:show-all'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'uuid')]

    private ?Uuid $uuid = null;

    #[ORM\Column]
    private ?bool $active = null;

    /**
     * @var Collection<int, Continent>
     */
    #[ORM\OneToMany(targetEntity: Continent::class, mappedBy: 'ofUser')]
    private Collection $continents;

    /**
     * @var Collection<int, Fruit>
     */
    #[ORM\OneToMany(targetEntity: Fruit::class, mappedBy: 'ofUser')]
    private Collection $fruits;

    /**
     * @var Collection<int, TypeFamily>
     */
    #[ORM\OneToMany(targetEntity: TypeFamily::class, mappedBy: 'ofUser')]
    private Collection $typeFamilies;

    public function __construct()
    {
        $this->continents = new ArrayCollection();
        $this->fruits = new ArrayCollection();
        $this->typeFamilies = new ArrayCollection();
    }

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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Continent>
     */
    public function getContinents(): Collection
    {
        return $this->continents;
    }

    public function addContinent(Continent $continent): static
    {
        if (!$this->continents->contains($continent)) {
            $this->continents->add($continent);
            $continent->setOfUser($this);
        }

        return $this;
    }

    public function removeContinent(Continent $continent): static
    {
        if ($this->continents->removeElement($continent)) {
            // set the owning side to null (unless already changed)
            if ($continent->getOfUser() === $this) {
                $continent->setOfUser(null);
            }
        }

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
            $fruit->setOfUser($this);
        }

        return $this;
    }

    public function removeFruit(Fruit $fruit): static
    {
        if ($this->fruits->removeElement($fruit)) {
            // set the owning side to null (unless already changed)
            if ($fruit->getOfUser() === $this) {
                $fruit->setOfUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TypeFamily>
     */
    public function getTypeFamilies(): Collection
    {
        return $this->typeFamilies;
    }

    public function addTypeFamily(TypeFamily $typeFamily): static
    {
        if (!$this->typeFamilies->contains($typeFamily)) {
            $this->typeFamilies->add($typeFamily);
            $typeFamily->setOfUser($this);
        }

        return $this;
    }

    public function removeTypeFamily(TypeFamily $typeFamily): static
    {
        if ($this->typeFamilies->removeElement($typeFamily)) {
            // set the owning side to null (unless already changed)
            if ($typeFamily->getOfUser() === $this) {
                $typeFamily->setOfUser(null);
            }
        }

        return $this;
    }
}
