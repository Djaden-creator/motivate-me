<?php

namespace App\Entity;

use App\Repository\FollowerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FollowerRepository::class)]
class Follower
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'followers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $sessionuser = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $offsessionuser = null;

    #[ORM\Column(length: 255)]
    private ?string $friendship = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSessionuser(): ?user
    {
        return $this->sessionuser;
    }

    public function setSessionuser(?user $sessionuser): static
    {
        $this->sessionuser = $sessionuser;

        return $this;
    }

    public function getOffsessionuser(): ?user
    {
        return $this->offsessionuser;
    }

    public function setOffsessionuser(user $offsessionuser): static
    {
        $this->offsessionuser = $offsessionuser;

        return $this;
    }

    public function getFriendship(): ?string
    {
        return $this->friendship;
    }

    public function setFriendship(string $friendship): static
    {
        $this->friendship = $friendship;

        return $this;
    }
}
