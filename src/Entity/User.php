<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
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

    #[ORM\Column(length: 45, nullable: true)]
    private ?string $lastLoginIp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastLoginUserAgent = null;

    /**
     * @var Collection<int, LoginAttempt>
     */
    #[ORM\OneToMany(targetEntity: LoginAttempt::class, mappedBy: 'user')]
    private Collection $ipAdress;

    public function __construct()
    {
        $this->ipAdress = new ArrayCollection();
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

    public function getLastLoginIp(): ?string
    {
        return $this->lastLoginIp;
    }

    public function setLastLoginIp(?string $lastLoginIp): static
    {
        $this->lastLoginIp = $lastLoginIp;

        return $this;
    } 



    public function getLastLoginUserAgent(): ?string
    {
        return $this->getLastLoginUserAgent;
    }

    public function setLastLoginUserAgent(?string $lastLoginUserAgent): static
    {
        $this->lastLoginUserAgent = $lastLoginUserAgent;

        return $this;
    }

    /**
     * @return Collection<int, LoginAttempt>
     */
    public function getIpAdress(): Collection
    {
        return $this->ipAdress;
    }

    public function addIpAdress(LoginAttempt $ipAdress): static
    {
        if (!$this->ipAdress->contains($ipAdress)) {
            $this->ipAdress->add($ipAdress);
            $ipAdress->setUser($this);
        }

        return $this;
    }

    public function removeIpAdress(LoginAttempt $ipAdress): static
    {
        if ($this->ipAdress->removeElement($ipAdress)) {
            // set the owning side to null (unless already changed)
            if ($ipAdress->getUser() === $this) {
                $ipAdress->setUser(null);
            }
        }

        return $this;
    }

}
