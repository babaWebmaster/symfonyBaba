<?php

namespace App\Entity;

use App\Repository\MaquetteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaquetteRepository::class)]
class Maquette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

   
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $subtitle = null;
    
    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, length: 150)]
    private ?string $shortDescription = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(nullable: true)]
    private ?string $imagePreview = null;

    private ?array $urlImagePreview = null;

    /**
     * @var Collection<int, CategoriesSite>
     */
    #[ORM\ManyToMany(targetEntity: CategoriesSite::class, inversedBy: 'yes')]
    private Collection $categoriesSite;

    public function __construct()
    {
        $this->categoriesSite = new ArrayCollection();
    }

   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeSite(): ?string
    {
        return $this->typeSite;
        
    }

    public function setTypeSite(string $typeSite): static
    {
        $this->typeSite = $typeSite;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(string $subtitle): static
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(string $shortDescription): static
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }



    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getImagePreview(): ?string
    {
        return $this->imagePreview;
    }

    public function setImagePreview(string|array $imagePreview): static
    {
        $this->imagePreview = $imagePreview;
        
        return $this;
    }

    public function setUrlImagePreview(array $urlImagePreview): static
    {
        $this->urlImagePreview = $urlImagePreview;

        return $this;
    }

    public function getUrlImagePreview(): ?array
    {
       return  $this->urlImagePreview;
        
    }

    /**
     * @return Collection<int, CategoriesSite>
     */
    public function getCategoriesSite(): Collection
    {
        return $this->categoriesSite;
    }

    public function addCategoriesSite(CategoriesSite $categoriesSite): static
    {
        if (!$this->categoriesSite->contains($categoriesSite)) {
            $this->categoriesSite->add($categoriesSite);
        }

        return $this;
    }

    public function removeCategoriesSite(CategoriesSite $categoriesSite): static
    {
        $this->categoriesSite->removeElement($categoriesSite);

        return $this;
    }
}
