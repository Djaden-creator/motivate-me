<?php

namespace App\Entity;

use App\Entity\Notification;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ShareingroupRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: ShareingroupRepository::class)]
class Shareingroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'shareingroups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Groups $groupid = null;

    #[ORM\ManyToOne(inversedBy: 'shareingroups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $posterownerid = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    /**
     * @var Collection<int, Likepostgroup>
     */
    #[ORM\OneToMany(targetEntity: Likepostgroup::class, mappedBy: 'postingroupid')]
    private Collection $postingroupid;

    /**
     * @var Collection<int, Commentgrouppost>
     */
    #[ORM\OneToMany(targetEntity: Commentgrouppost::class, mappedBy: 'postgroupid')]
    private Collection $commentgroupposts;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'shareingroupid')]
    private Collection $notifications;

    
    public function __construct()
    {
        $this->postingroupid = new ArrayCollection();
        $this->commentgroupposts = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPosterownerid(): ?User
    {
        return $this->posterownerid;
    }

    public function setPosterownerid(?User $posterownerid): static
    {
        $this->posterownerid = $posterownerid;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

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
     * Summary of getname of the group
     * 
     */
    public function getfolder() 
    {

        $getnamehere=$this->getGroupid()->getGroupname();

        return $getnamehere;
    }
    
    /**
     * Summary of getImagePath
     * 
     */
    public function getImagepath() 
    {
        return 'imagegroupcontent/'.$this->getfolder().'/'.$this->getImage();
    }

    /**
     * @return Collection<int, Likepostgroup>
     */
    public function getPostingroupid(): Collection
    {
        return $this->postingroupid;
    }

    public function addPostingroupid(Likepostgroup $postingroupid): static
    {
        if (!$this->postingroupid->contains($postingroupid)) {
            $this->postingroupid->add($postingroupid);
            $postingroupid->setPostingroupid($this);
        }

        return $this;
    }

    public function removePostingroupid(Likepostgroup $postingroupid): static
    {
        if ($this->postingroupid->removeElement($postingroupid)) {
            // set the owning side to null (unless already changed)
            if ($postingroupid->getPostingroupid() === $this) {
                $postingroupid->setPostingroupid(null);
            }
        }

        return $this;
    }

    /**
     * this code allow to know if the post of the group is liked by the user session
     * @param \App\Entity\User $user
     * @return bool
     */
    public function isLikeagrouppost(User $user)
    {     
      foreach ($this->postingroupid as $post) {
      if($post->getUserid()===$user) return true;
    }
     return false;
    }

    /**
     * @return Collection<int, Commentgrouppost>
     */
    public function getCommentgroupposts(): Collection
    {
        return $this->commentgroupposts;
    }

    public function addCommentgrouppost(Commentgrouppost $commentgrouppost): static
    {
        if (!$this->commentgroupposts->contains($commentgrouppost)) {
            $this->commentgroupposts->add($commentgrouppost);
            $commentgrouppost->setPostgroupid($this);
        }

        return $this;
    }

    public function removeCommentgrouppost(Commentgrouppost $commentgrouppost): static
    {
        if ($this->commentgroupposts->removeElement($commentgrouppost)) {
            // set the owning side to null (unless already changed)
            if ($commentgrouppost->getPostgroupid() === $this) {
                $commentgrouppost->setPostgroupid(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setShareingroupid($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getShareingroupid() === $this) {
                $notification->setShareingroupid(null);
            }
        }

        return $this;
    }
}
