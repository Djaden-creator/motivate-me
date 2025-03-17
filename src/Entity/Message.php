<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'messageuserone')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userone = null;

    #[ORM\ManyToOne(inversedBy: 'messageusertwo')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $usertwo = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserone(): ?User
    {
        return $this->userone;
    }

    public function setUserone(?User $userone): static
    {
        $this->userone = $userone;

        return $this;
    }

    public function getUsertwo(): ?User
    {
        return $this->usertwo;
    }

    public function setUsertwo(?User $usertwo): static
    {
        $this->usertwo = $usertwo;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
