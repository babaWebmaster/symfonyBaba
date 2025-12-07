<?php

namespace App\Entity;

use App\Repository\SeoMetadataRepository;
use App\Enum\FollowGoogle;   
use App\Enum\IndexGoogle;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SeoMetadataRepository::class)]
class SeoMetadata
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ogTitle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $ogDescription = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ogImage = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $schemaJson = null;

     #[ORM\Column(enumType: FollowGoogle::class, nullable: false)]
    private ?FollowGoogle  $followGoogle = FollowGoogle::FOLLOW;

    #[ORM\Column(enumType: IndexGoogle::class, nullable: false)]
    private ?IndexGoogle $indexGoogle = IndexGoogle::INDEX;

    #[ORM\OneToOne(inversedBy: 'seoMetadata', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $post = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getOgTitle(): ?string
    {
        return $this->ogTitle;
    }

    public function setOgTitle(?string $ogTitle): static
    {
        $this->ogTitle = $ogTitle;

        return $this;
    }

    public function getOgDescription(): ?string
    {
        return $this->ogDescription;
    }

    public function setOgDescription(?string $ogDescription): static
    {
        $this->ogDescription = $ogDescription;

        return $this;
    }

    public function getOgImage(): ?string
    {
        return $this->ogImage;
    }

    public function setOgImage(?string $ogImage): static
    {
        $this->ogImage = $ogImage;

        return $this;
    }

    public function getSchemaJson(): ?string
    {
        return $this->schemaJson;
    }

    public function setSchemaJson(?string $schemaJson): static
    {
        $this->schemaJson = $schemaJson;

        return $this;
    }

    public function getFollowGoogle():? FollowGoogle
    {
        return $this->followGoogle;
    }

    public function setFollowGoogle(FollowGoogle $followGoogle): self
    {
        $this->followGoogle = $followGoogle;

        return $this;
    }

    public function getIndexGoogle():? IndexGoogle
    {
        return $this->indexGoogle;
    }

    public function setIndexGoogle(IndexGoogle $indexGoogle): self
    {
        $this->indexGoogle = $indexGoogle;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(Post $post): static
    {
        $this->post = $post;

        return $this;
    }
}
