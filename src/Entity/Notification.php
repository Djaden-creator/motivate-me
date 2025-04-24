<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $usertonotifie = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column]
    private ?bool $IsRead = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Shareingroup $shareingroupid = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsertonotifie(): ?User
    {
        return $this->usertonotifie;
    }

    public function setUsertonotifie(?User $usertonotifie): static
    {
        $this->usertonotifie = $usertonotifie;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function isRead(): ?bool
    {
        return $this->IsRead;
    }

    public function setIsRead(bool $IsRead): static
    {
        $this->IsRead = $IsRead;

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

    public function getShareingroupid(): ?Shareingroup
    {
        return $this->shareingroupid;
    }

    public function setShareingroupid(?Shareingroup $shareingroupid): static
    {
        $this->shareingroupid = $shareingroupid;

        return $this;
    }
}
