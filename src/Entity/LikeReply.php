<?php

namespace App\Entity;

use App\Repository\LikeReplyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikeReplyRepository::class)]
class LikeReply
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'likeReplies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $userid = null;

    #[ORM\ManyToOne(inversedBy: 'likeReplies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ReplyComment $replyid = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdat = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getReplyid(): ?ReplyComment
    {
        return $this->replyid;
    }

    public function setReplyid(?ReplyComment $replyid): static
    {
        $this->replyid = $replyid;

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
