<?php

namespace App\Entity;

use App\Repository\VotecommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VotecommentRepository::class)]
class Votecomment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'votecomments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userid = null;

    #[ORM\ManyToOne(inversedBy: 'votecomments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commentgrouppost $commentpostingroupid = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserid(): ?User
    {
        return $this->userid;
    }

    public function setUserid(?User $userid): static
    {
        $this->userid = $userid;

        return $this;
    }

    public function getCommentpostingroupid(): ?Commentgrouppost
    {
        return $this->commentpostingroupid;
    }

    public function setCommentpostingroupid(?Commentgrouppost $commentpostingroupid): static
    {
        $this->commentpostingroupid = $commentpostingroupid;

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
