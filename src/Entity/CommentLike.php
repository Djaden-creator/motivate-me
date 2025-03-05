<?php

namespace App\Entity;

use App\Repository\CommentLikeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentLikeRepository::class)]
class CommentLike
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'commentLikes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?commentaires $commentid = null;

    #[ORM\ManyToOne(inversedBy: 'userid')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $userid = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommentid(): ?commentaires
    {
        return $this->commentid;
    }

    public function setCommentid(?commentaires $commentid): static
    {
        $this->commentid = $commentid;

        return $this;
    }

    public function getUserid(): ?user
    {
        return $this->userid;
    }

    public function setUserid(?user $userid): static
    {
        $this->userid = $userid;

        return $this;
    }

    public function getCreatedat(): ?\DateTimeInterface
    {
        return $this->createdat;
    }

    public function setCreatedat(\DateTimeInterface $createdat): static
    {
        $this->createdat = $createdat;

        return $this;
    }
}
