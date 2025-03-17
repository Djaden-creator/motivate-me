<?php

namespace App\Entity;

use App\Repository\FollowingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FollowingRepository::class)]
class Following
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'followings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $usersessionid = null;

    #[ORM\ManyToOne(inversedBy: 'offuserfollowers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $offuserid = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsersessionid(): ?user
    {
        return $this->usersessionid;
    }

    public function setUsersessionid(?user $usersessionid): static
    {
        $this->usersessionid = $usersessionid;

        return $this;
    }

    public function getOffuserid(): ?user
    {
        return $this->offuserid;
    }

    public function setOffuserid(?user $offuserid): static
    {
        $this->offuserid = $offuserid;

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
