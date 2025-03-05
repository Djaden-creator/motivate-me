<?php

namespace App\Entity;

use App\Repository\CommentairesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentairesRepository::class)]
class Commentaires
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    private ?user $iduser = null;

    #[ORM\ManyToOne(inversedBy: 'comment')]
    private ?article $idarticle = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    /**
     * @var Collection<int, CommentLike>
     */
    #[ORM\OneToMany(targetEntity: CommentLike::class, mappedBy: 'commentid')]
    private Collection $commentLikes;

    /**
     * @var Collection<int, ReplyComment>
     */
    #[ORM\OneToMany(targetEntity: ReplyComment::class, mappedBy: 'comment')]
    private Collection $replyComments; 

    public function __construct()
    {
        $this->commentLikes = new ArrayCollection();
        $this->replyComments = new ArrayCollection();
      
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIduser(): ?user
    {
        return $this->iduser;
    }

    public function setIduser(?user $iduser): static
    {
        $this->iduser = $iduser;

        return $this;
    }

    public function getIdarticle(): ?article
    {
        return $this->idarticle;
    }

    public function setIdarticle(?article $idarticle): static
    {
        $this->idarticle = $idarticle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }
 
    /**
     * here we get name of the commenter
     * @return string
     */
    public function getname(): string
    {
        return $this->iduser->getUsername(); 
    }

    /**
     * here we get name picture the commeneter
     * @return string
     */
    public function getimagepath(): string
    {
        return $this->iduser->getImagepath(); 
    }
    
    /**
     * @return Collection<int, CommentLike>
     */
    public function getCommentLikes(): Collection
    {
        return $this->commentLikes;
    }

    public function addCommentLike(CommentLike $commentLike): static
    {
        if (!$this->commentLikes->contains($commentLike)) {
            $this->commentLikes->add($commentLike);
            $commentLike->setCommentid($this);
        }

        return $this;
    }

    public function removeCommentLike(CommentLike $commentLike): static
    {
        if ($this->commentLikes->removeElement($commentLike)) {
            // set the owning side to null (unless already changed)
            if ($commentLike->getCommentid() === $this) {
                $commentLike->setCommentid(null);
            }
        }

        return $this;
    }

    /**
     * this function return a user comment like
     * @param \App\Entity\User $user
     * @return bool
     */
    public function isLikecommentbyUser(User $user): bool
    {
        foreach ($this->commentLikes as $commentLike) {
            if($commentLike->getUserid()===$user) return true;
        }
        return false;
    }

    /**
     * @return Collection<int, ReplyComment>
     */
    public function getReplyComments(): Collection
    {
        return $this->replyComments;
    }

    public function addReplyComment(ReplyComment $replyComment): static
    {
        if (!$this->replyComments->contains($replyComment)) {
            $this->replyComments->add($replyComment);
            $replyComment->setComment($this);
        }

        return $this;
    }

    public function removeReplyComment(ReplyComment $replyComment): static
    {
        if ($this->replyComments->removeElement($replyComment)) {
            // set the owning side to null (unless already changed)
            if ($replyComment->getComment() === $this) {
                $replyComment->setComment(null);
            }
        }

        return $this;
    }
}
