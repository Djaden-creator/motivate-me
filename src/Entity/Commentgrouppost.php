<?php

namespace App\Entity;

use App\Repository\CommentgrouppostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentgrouppostRepository::class)]
class Commentgrouppost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'commentgroupposts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userid = null;

    #[ORM\ManyToOne(inversedBy: 'commentgroupposts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Shareingroup $postgroupid = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $comment = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    /**
     * @var Collection<int, Votecomment>
     */
    #[ORM\OneToMany(targetEntity: Votecomment::class, mappedBy: 'commentpostingroupid')]
    private Collection $votecomments;

    public function __construct()
    {
        $this->votecomments = new ArrayCollection();
    }

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

    public function getPostgroupid(): ?Shareingroup
    {
        return $this->postgroupid;
    }

    public function setPostgroupid(?Shareingroup $postgroupid): static
    {
        $this->postgroupid = $postgroupid;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

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

    /**
     * @return Collection<int, Votecomment>
     */
    public function getVotecomments(): Collection
    {
        return $this->votecomments;
    }

    public function addVotecomment(Votecomment $votecomment): static
    {
        if (!$this->votecomments->contains($votecomment)) {
            $this->votecomments->add($votecomment);
            $votecomment->setCommentpostingroupid($this);
        }

        return $this;
    }

    public function removeVotecomment(Votecomment $votecomment): static
    {
        if ($this->votecomments->removeElement($votecomment)) {
            // set the owning side to null (unless already changed)
            if ($votecomment->getCommentpostingroupid() === $this) {
                $votecomment->setCommentpostingroupid(null);
            }
        }

        return $this;
    }

    public function isVotedby(User $user)
    {
       foreach($this->votecomments as $myvote){
          if($myvote->getUserid()===$user) return true;
       } 
       return false;
    }
}
