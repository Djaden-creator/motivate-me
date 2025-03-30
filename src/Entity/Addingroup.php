<?php

namespace App\Entity;

use App\Repository\AddingroupRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddingroupRepository::class)]
class Addingroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'addedby')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $addedby = null;

    #[ORM\ManyToOne(inversedBy: 'newmemberid')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $newmember = null;

    #[ORM\ManyToOne(inversedBy: 'addingroups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Groups $groupid = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddedby(): ?User
    {
        return $this->addedby;
    }

    public function setAddedby(?User $addedby): static
    {
        $this->addedby = $addedby;

        return $this;
    }

    public function getNewmember(): ?User
    {
        return $this->newmember;
    }

    public function setNewmember(?User $newmember): static
    {
        $this->newmember = $newmember;

        return $this;
    }

    public function getGroupid(): ?Groups
    {
        return $this->groupid;
    }

    public function setGroupid(?Groups $groupid): static
    {
        $this->groupid = $groupid;

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
}
