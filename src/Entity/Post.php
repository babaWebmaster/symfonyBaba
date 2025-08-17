<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 50)]
    private ?string $postType = null;

    #[ORM\Column(nullable: true)]
    private ?int $entityId = null;

    #[ORM\OneToOne(mappedBy: 'post', targetEntity: SeoMetadata::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private ?SeoMetadata $seoMetadata = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPostType(): ?string
    {
        return $this->postType;
    }

    public function setPostType(string $postType): static
    {
        $this->postType = $postType;

        return $this;
    }

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    public function setEntityId(?int $entityId): static
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getSeoMetadata(): ?SeoMetadata
    {
        if ($this->seoMetadata === null) {
            $this->seoMetadata = new SeoMetadata();
        }

        return $this->seoMetadata;
    }

    public function setSeoMetadata(SeoMetadata $seoMetadata): static
    {
        // set the owning side of the relation if necessary
        if ($seoMetadata->getPost() !== $this) {
            $seoMetadata->setPost($this);
        }

        $this->seoMetadata = $seoMetadata;

        return $this;
    }

    public function __toString(): string
    {
        // Cela permettra à EasyAdmin de bien identifier les posts dans les sélecteurs
        return $this->getSlug() ?: 'Nouveau Post';
    }
}
