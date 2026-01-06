<?php

namespace App\Entity;

use App\Enum\Target;
use App\Repository\AnnoncesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnoncesRepository::class)]
class Annonces
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'annonces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sent_by = null;

    #[ORM\Column(enumType: Target::class)]
    private ?Target $sent_to = null;

    #[ORM\Column]
    private ?\DateTime $sent_at = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getSentBy(): ?User
    {
        return $this->sent_by;
    }

    public function setSentBy(?User $sent_by): static
    {
        $this->sent_by = $sent_by;

        return $this;
    }

    public function getSentTo(): ?Target
    {
        return $this->sent_to;
    }

    public function setSentTo(Target $sent_to): static
    {
        $this->sent_to = $sent_to;

        return $this;
    }

    public function getSentAt(): ?\DateTime
    {
        return $this->sent_at;
    }

    public function setSentAt(\DateTime $sent_at): static
    {
        $this->sent_at = $sent_at;

        return $this;
    }
}
