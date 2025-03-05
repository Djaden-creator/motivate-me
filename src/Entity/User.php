<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PhpParser\Node\Stmt\Function_;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'il y\'a deja un compte existant avec cet email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $gender = null;

    /**
     * @var Collection<int, Articlelike>
     */
    #[ORM\OneToMany(targetEntity: Articlelike::class, mappedBy: 'userid')]
    private Collection $Articleid;

    /**
     * @var Collection<int, Article>
     */
    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'userposter')]
    private Collection $posteruser;

    /**
     * @var Collection<int, Commentaires>
     */
    #[ORM\OneToMany(targetEntity: Commentaires::class, mappedBy: 'iduser')]
    private Collection $commentaires;

    /**
     * @var Collection<int, CommentLike>
     */
    #[ORM\OneToMany(targetEntity: CommentLike::class, mappedBy: 'userid')]
    private Collection $userid;

    /**
     * @var Collection<int, ReplyComment>
     */
    #[ORM\OneToMany(targetEntity: ReplyComment::class, mappedBy: 'userid')]
    private Collection $replyComments;

    /**
     * @var Collection<int, LikeReply>
     */
    #[ORM\OneToMany(targetEntity: LikeReply::class, mappedBy: 'userid')]
    private Collection $likeReplies;

    /**
     * @var Collection<int, Follower>
     */
    #[ORM\OneToMany(targetEntity: Follower::class, mappedBy: 'sessionuser')]
    private Collection $followers;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $religion = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $since = null;

    /**
     * @var Collection<int, Subcart>
     */
    #[ORM\OneToMany(targetEntity: Subcart::class, mappedBy: 'user')]
    private Collection $subcarts;

    /**
     * @var Collection<int, Addtocart>
     */
    #[ORM\OneToMany(targetEntity: Addtocart::class, mappedBy: 'userid')]
    private Collection $addtocarts;

    /**
     * @var Collection<int, Orderdetails>
     */
    #[ORM\OneToMany(targetEntity: Orderdetails::class, mappedBy: 'user')]
    private Collection $orderdetails;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripeidtoken = null;

    /**
     * @var Collection<int, Commande>
     */
    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'user')]
    private Collection $commandes;

    /**
     * @var Collection<int, Religion>
     */
    #[ORM\OneToMany(targetEntity: Religion::class, mappedBy: 'user')]
    private Collection $religions;

    /**
     * @var Collection<int, Sauvegarde>
     */
    #[ORM\OneToMany(targetEntity: Sauvegarde::class, mappedBy: 'user')]
    private Collection $sauvegardes;

    /**
     * @var Collection<int, Motivateur>
     */
    #[ORM\OneToMany(targetEntity: Motivateur::class, mappedBy: 'user')]
    private Collection $motivateurs;

    public function __construct()
    {
        $this->Articleid = new ArrayCollection();
        $this->posteruser = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->userid = new ArrayCollection();
        $this->replyComments = new ArrayCollection();
        $this->likeReplies = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->subcarts = new ArrayCollection();
        $this->addtocarts = new ArrayCollection();
        $this->orderdetails = new ArrayCollection();
        $this->commandes = new ArrayCollection();
        $this->religions = new ArrayCollection();
        $this->sauvegardes = new ArrayCollection();
        $this->motivateurs = new ArrayCollection();
       
    }
    public function __toString():string
    {
        return $this->getUsername();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return Collection<int, Articlelike>
     */
    public function getArticleid(): Collection
    {
        return $this->Articleid;
    }

    public function addArticleid(Articlelike $articleid): static
    {
        if (!$this->Articleid->contains($articleid)) {
            $this->Articleid->add($articleid);
            $articleid->setUserid($this);
        }

        return $this;
    }

    public function removeArticleid(Articlelike $articleid): static
    {
        if ($this->Articleid->removeElement($articleid)) {
            // set the owning side to null (unless already changed)
            if ($articleid->getUserid() === $this) {
                $articleid->setUserid(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getPosteruser(): Collection
    {
        return $this->posteruser;
    }

    public function addPosteruser(Article $posteruser): static
    {
        if (!$this->posteruser->contains($posteruser)) {
            $this->posteruser->add($posteruser);
            $posteruser->setUserposter($this);
        }

        return $this;
    }

    public function removePosteruser(Article $posteruser): static
    {
        if ($this->posteruser->removeElement($posteruser)) {
            // set the owning side to null (unless already changed)
            if ($posteruser->getUserposter() === $this) {
                $posteruser->setUserposter(null);
            }
        }

        return $this;
    }  

   /**
    * Summary of getuserforoff
    * here to get hte user of offline session id user 
    * @param \App\Entity\User $user
    * @return User
    */
   public function getuserforoff(){
    if($this->getId()){
        return $this->getId();
    }
   }

   /**
    * @return Collection<int, Commentaires>
    */
   public function getCommentaires(): Collection
   {
       return $this->commentaires;
   }

   public function addCommentaire(Commentaires $commentaire): static
   {
       if (!$this->commentaires->contains($commentaire)) {
           $this->commentaires->add($commentaire);
           $commentaire->setIduser($this);
       }

       return $this;
   }

   public function removeCommentaire(Commentaires $commentaire): static
   {
       if ($this->commentaires->removeElement($commentaire)) {
           // set the owning side to null (unless already changed)
           if ($commentaire->getIduser() === $this) {
               $commentaire->setIduser(null);
           }
       }

       return $this;
   }

   /**
    * @return Collection<int, CommentLike>
    */
   public function getUserid(): Collection
   {
       return $this->userid;
   }

   public function addUserid(CommentLike $userid): static
   {
       if (!$this->userid->contains($userid)) {
           $this->userid->add($userid);
           $userid->setUserid($this);
       }

       return $this;
   }

   public function removeUserid(CommentLike $userid): static
   {
       if ($this->userid->removeElement($userid)) {
           // set the owning side to null (unless already changed)
           if ($userid->getUserid() === $this) {
               $userid->setUserid(null);
           }
       }

       return $this;
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
           $replyComment->setUserid($this);
       }

       return $this;
   }

   public function removeReplyComment(ReplyComment $replyComment): static
   {
       if ($this->replyComments->removeElement($replyComment)) {
           // set the owning side to null (unless already changed)
           if ($replyComment->getUserid() === $this) {
               $replyComment->setUserid(null);
           }
       }

       return $this;
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
           $likeReply->setUserid($this);
       }

       return $this;
   }

   public function removeLikeReply(LikeReply $likeReply): static
   {
       if ($this->likeReplies->removeElement($likeReply)) {
           // set the owning side to null (unless already changed)
           if ($likeReply->getUserid() === $this) {
               $likeReply->setUserid(null);
           }
       }

       return $this;
   }

   /**
    * @return Collection<int, Follower>
    */
   public function getFollowers(): Collection
   {
       return $this->followers;
   }

   public function addFollower(Follower $follower): static
   {
       if (!$this->followers->contains($follower)) {
           $this->followers->add($follower);
           $follower->setSessionuser($this);
       }

       return $this;
   }

   public function removeFollower(Follower $follower): static
   {
       if ($this->followers->removeElement($follower)) {
           // set the owning side to null (unless already changed)
           if ($follower->getSessionuser() === $this) {
               $follower->setSessionuser(null);
           }
       }

       return $this;
   }

   /**
     * this code allow to know if the user is followed by the user session
     * @param \App\Entity\User $user
     * @return bool
     */
    public function isFollowedBy(UserInterface $userInterface): bool
    {    foreach ($this->followers as $followers) {
            if($followers->getSessionuser()===$userInterface) return true;
        }
        return false;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getReligion(): ?string
    {
        return $this->religion;
    }

    public function setReligion(?string $religion): static
    {
        $this->religion = $religion;

        return $this;
    }

    public function getSince(): ?\DateTimeInterface
    {
        return $this->since;
    }

    public function setSince(\DateTimeInterface $since): static
    {
        $this->since = $since;

        return $this;
    }

    /**
     * Summary of getfolder
     * 
     */
    public function getfolder() 
    {

        $getmail=$this->getEmail();

        return $getmail;
    }
    
    /**
     * Summary of getImagePath
     * 
     */
    public function getImagepath() 
    {
        return 'userpicture/'.$this->getfolder().'/'.$this->getPicture();
    }

    /**
     * @return Collection<int, Subcart>
     */
    public function getSubcarts(): Collection
    {
        return $this->subcarts;
    }

    public function addSubcart(Subcart $subcart): static
    {
        if (!$this->subcarts->contains($subcart)) {
            $this->subcarts->add($subcart);
            $subcart->setUser($this);
        }

        return $this;
    }

    public function removeSubcart(Subcart $subcart): static
    {
        if ($this->subcarts->removeElement($subcart)) {
            // set the owning side to null (unless already changed)
            if ($subcart->getUser() === $this) {
                $subcart->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Addtocart>
     */
    public function getAddtocarts(): Collection
    {
        return $this->addtocarts;
    }

    public function addAddtocart(Addtocart $addtocart): static
    {
        if (!$this->addtocarts->contains($addtocart)) {
            $this->addtocarts->add($addtocart);
            $addtocart->setUserid($this);
        }

        return $this;
    }

    public function removeAddtocart(Addtocart $addtocart): static
    {
        if ($this->addtocarts->removeElement($addtocart)) {
            // set the owning side to null (unless already changed)
            if ($addtocart->getUserid() === $this) {
                $addtocart->setUserid(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Orderdetails>
     */
    public function getOrderdetails(): Collection
    {
        return $this->orderdetails;
    }

    public function addOrderdetail(Orderdetails $orderdetail): static
    {
        if (!$this->orderdetails->contains($orderdetail)) {
            $this->orderdetails->add($orderdetail);
            $orderdetail->setUser($this);
        }

        return $this;
    }

    public function removeOrderdetail(Orderdetails $orderdetail): static
    {
        if ($this->orderdetails->removeElement($orderdetail)) {
            // set the owning side to null (unless already changed)
            if ($orderdetail->getUser() === $this) {
                $orderdetail->setUser(null);
            }
        }

        return $this;
    }

    public function getStripeidtoken(): ?string
    {
        return $this->stripeidtoken;
    }

    public function setStripeidtoken(?string $stripeidtoken): static
    {
        $this->stripeidtoken = $stripeidtoken;

        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setUser($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getUser() === $this) {
                $commande->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Religion>
     */
    public function getReligions(): Collection
    {
        return $this->religions;
    }

    public function addReligion(Religion $religion): static
    {
        if (!$this->religions->contains($religion)) {
            $this->religions->add($religion);
            $religion->setUser($this);
        }

        return $this;
    }

    public function removeReligion(Religion $religion): static
    {
        if ($this->religions->removeElement($religion)) {
            // set the owning side to null (unless already changed)
            if ($religion->getUser() === $this) {
                $religion->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sauvegarde>
     */
    public function getSauvegardes(): Collection
    {
        return $this->sauvegardes;
    }

    public function addSauvegarde(Sauvegarde $sauvegarde): static
    {
        if (!$this->sauvegardes->contains($sauvegarde)) {
            $this->sauvegardes->add($sauvegarde);
            $sauvegarde->setUser($this);
        }

        return $this;
    }

    public function removeSauvegarde(Sauvegarde $sauvegarde): static
    {
        if ($this->sauvegardes->removeElement($sauvegarde)) {
            // set the owning side to null (unless already changed)
            if ($sauvegarde->getUser() === $this) {
                $sauvegarde->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Motivateur>
     */
    public function getMotivateurs(): Collection
    {
        return $this->motivateurs;
    }

    public function addMotivateur(Motivateur $motivateur): static
    {
        if (!$this->motivateurs->contains($motivateur)) {
            $this->motivateurs->add($motivateur);
            $motivateur->setUser($this);
        }

        return $this;
    }

    public function removeMotivateur(Motivateur $motivateur): static
    {
        if ($this->motivateurs->removeElement($motivateur)) {
            // set the owning side to null (unless already changed)
            if ($motivateur->getUser() === $this) {
                $motivateur->setUser(null);
            }
        }

        return $this;
    }
}
