<?php

namespace App\Entity;

use App\Repository\CategoriesSiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriesSiteRepository::class)]
class CategoriesSite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 75)]
    private ?string $type = null;

    /**
     * @var Collection<int, Maquette>
     */
    #[ORM\ManyToMany(targetEntity: Maquette::class, mappedBy: 'categoriesSite')]
    private Collection $yes;

    public function __construct()
    {
        $this->yes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Maquette>
     */
    public function getYes(): Collection
    {
        return $this->yes;
    }

    public function addYe(Maquette $ye): static
    {
        if (!$this->yes->contains($ye)) {
            $this->yes->add($ye);
            $ye->addCategoriesSite($this);
        }

        return $this;
    }

    public function removeYe(Maquette $ye): static
    {
        if ($this->yes->removeElement($ye)) {
            $ye->removeCategoriesSite($this);
        }

        return $this;
    }

    public function __toString():string 
    {
        return $this->type ?? '';
    }
}
