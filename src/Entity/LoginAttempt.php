<?php

namespace App\Entity;

use App\Repository\LoginAttemptRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoginAttemptRepository::class)]
class LoginAttempt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ipAdress')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 45)]
    private ?string $ipAddress = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $userAgent = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $loggedInAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country=null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city=null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): static
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): static
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getLoggedInAt(): ?\DateTimeImmutable
    {
        return $this->loggedInAt;
    }

    public function setLoggedInAt(\DateTimeImmutable $loggedInAt): static
    {
        $this->loggedInAt = $loggedInAt;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;

    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }
}
