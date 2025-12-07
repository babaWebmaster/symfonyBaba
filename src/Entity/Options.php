<?php

namespace App\Entity;

use App\Repository\OptionsRepository;
use App\Enum\OptionType;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: OptionsRepository::class)]
class Options
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $value = null;

    #[ORM\Column(enumType: OptionType::class)]
    private ?OptionType $OptionType = OptionType::STRING;

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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getOptionType(): ?OptionType
    {
        return $this->OptionType;
    }

    public function setOptionType(OptionType $OptionType): static
    {
        $this->OptionType = $OptionType;

        return $this;
    }
}
