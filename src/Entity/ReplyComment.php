<?php

namespace App\Entity;

use App\Repository\ReplyCommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReplyCommentRepository::class)]
class ReplyComment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'replyComments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $userid = null;

    #[ORM\ManyToOne(inversedBy: 'replyComments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?commentaires $comment = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descriptionreply = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created = null;

    /**
     * @var Collection<int, LikeReply>
     */
    #[ORM\OneToMany(targetEntity: LikeReply::class, mappedBy: 'replyid')]
    private Collection $likeReplies;

    public function __construct()
    {
        $this->likeReplies = new ArrayCollection();
    }

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

    public function getComment(): ?commentaires
    {
        return $this->comment;
    }

    public function setComment(?commentaires $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getDescriptionreply(): ?string
    {
        return $this->descriptionreply;
    }

    public function setDescriptionreply(string $descriptionreply): static
    {
        $this->descriptionreply = $descriptionreply;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): static
    {
        $this->created = $created;

        return $this;
    }

    /**
     * here we get name pf the commeneter
     * @return string
     */
    public function getname(): string
    {
        return $this->userid->getUsername(); 
    }

    /**
     * here we get picture of the commeneter
     * @return string
     */
    public function getpicture(): string
    {
        return $this->userid->getImagepath(); 
    }

    /**
     * @return Collection<int, LikeReply>
     */
    public function getLikeReplies(): Collection
    {
        return $this->likeReplies;
    }

    public function addLikeReply(LikeReply $likeReply): static
    {
        if (!$this->likeReplies->contains($likeReply)) {
            $this->likeReplies->add($likeReply);
            $likeReply->setReplyid($this);
        }

        return $this;
    }

    public function removeLikeReply(LikeReply $likeReply): static
    {
        if ($this->likeReplies->removeElement($likeReply)) {
            // set the owning side to null (unless already changed)
            if ($likeReply->getReplyid() === $this) {
                $likeReply->setReplyid(null);
            }
        }

        return $this;
    }

    /**
     * this function return a user reply comment  likes
     * @param \App\Entity\User $user
     * @return bool
     */
    public function isLikereplycommentbyUser(User $user): bool
    {
        foreach ($this->likeReplies as $likereply) {
            if($likereply->getUserid()==$user) return true;
        }
        return false;
    }

}
