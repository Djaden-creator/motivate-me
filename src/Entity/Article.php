<?php

namespace App\Entity;
use App\Entity\User;
use App\Entity\Articlelike;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[Vich\Uploadable]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;


    #[ORM\Column(type: Types::TEXT)]
    private ?string $topic = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column(type: 'string')]
    private ?string $pictures = null;

    /**
     * @var Collection<int, Articlelike>
     */
    #[ORM\OneToMany(targetEntity: Articlelike::class, mappedBy: 'postid')]
    private Collection $articlelikes;

    #[ORM\ManyToOne(inversedBy: 'posteruser')]
    private ?User $userposter = null;

    /**
     * @var Collection<int, Commentaires>
     */
    #[ORM\OneToMany(targetEntity: Commentaires::class, mappedBy: 'idarticle')]
    private Collection $comment;

    #[ORM\Column(length: 255)]
    private ?string $category = null;

    #[ORM\Column]
    private ?float $price = null;

    /**
     * @var Collection<int, Orderdetails>
     */
    #[ORM\OneToMany(targetEntity: Orderdetails::class, mappedBy: 'articleid')]
    private Collection $orderdetails;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    /**
     * @var Collection<int, Commande>
     */
    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'article')]
    private Collection $commandes;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $vue = null;

    /**
     * @var Collection<int, Sauvegarde>
     */
    #[ORM\OneToMany(targetEntity: Sauvegarde::class, mappedBy: 'article')]
    private Collection $sauvegardes;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fichier = null;

    public function __construct()
    {
        $this->articlelikes = new ArrayCollection();
        $this->comment = new ArrayCollection();
        $this->orderdetails = new ArrayCollection();
        $this->commandes = new ArrayCollection();
        $this->sauvegardes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getTopic(): ?string
    {
        return $this->topic;
    }

    public function setTopic(string $topic): static
    {
        $this->topic = $topic;

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
     * @return Collection<int, Articlelike>
     */
    public function getArticlelikes(): Collection
    {
        return $this->articlelikes;
    }

    public function addArticlelike(Articlelike $articlelike): static
    {
        if (!$this->articlelikes->contains($articlelike)) {
            $this->articlelikes->add($articlelike);
            $articlelike->setPostid($this);
        }

        return $this;
    }

    public function removeArticlelike(Articlelike $articlelike): static
    {
        if ($this->articlelikes->removeElement($articlelike)) {
            // set the owning side to null (unless already changed)
            if ($articlelike->getPostid() === $this) {
                $articlelike->setPostid(null);
            }
        }

        return $this;
    }

    /**
     * this code allow to know if the article is liked by the user session
     * @param \App\Entity\User $user
     * @return bool
     */
    public function IslikeByUser(User $user): bool
    { foreach ($this->articlelikes as $articlelike) {
            if($articlelike->getUserid()===$user) return true;
        }
        return false;
    }

    public function getPictures(): ?string
    {
        return $this->pictures;
    }

    public function setPictures(string $pictures): static
    {
        $this->pictures = $pictures;

        return $this;
    }

    public function getUserposter(): ?User
    {
        return $this->userposter;
    }

    public function setUserposter(?User $userposter): static
    {
        $this->userposter = $userposter;

        return $this;
    }


    /**
     * Summary of getname
     * here to get the name of a user who posted the article
     * @param \App\Entity\User|null $user
     * @return string
     */
    public function getname(User $user=null){
        if($user==$this->userposter){
            return $user->getUsername();
        }
    }

    /**
     * Summary of getuserposterid
     * here to get the id of the user who posted the article
     * @param \App\Entity\User|null $user
     * @return int|null
     */
    public function getuserposterid(User $user=null){
        if($user==$this->userposter){
            return $user->getId();
        }
    }

    /**
     * @return Collection<int, Commentaires>
     */
    public function getComment(): Collection
    {
        return $this->comment;
    }

    public function addComment(Commentaires $comment): static
    {
        if (!$this->comment->contains($comment)) {
            $this->comment->add($comment);
            $comment->setIdarticle($this);
        }

        return $this;
    }

    public function removeComment(Commentaires $comment): static
    {
        if ($this->comment->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getIdarticle() === $this) {
                $comment->setIdarticle(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }



    /**
     * Summary of getfolder
     * 
     */
    public function getfolder() 
    {

        $getmail=$this->getUserposter()->getEmail();

        return $getmail;
    }
    
    /**
     * Summary of getImagePath
     * 
     */
    public function getImagepath() 
    {
        return 'uploadarticle/'.$this->getfolder().'/'.$this->getPictures();
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

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
            $orderdetail->setArticleid($this);
        }

        return $this;
    }

    public function removeOrderdetail(Orderdetails $orderdetail): static
    {
        if ($this->orderdetails->removeElement($orderdetail)) {
            // set the owning side to null (unless already changed)
            if ($orderdetail->getArticleid() === $this) {
                $orderdetail->setArticleid(null);
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
            $commande->setArticle($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getArticle() === $this) {
                $commande->setArticle(null);
            }
        }

        return $this;
    }

    public function getVue(): ?string
    {
        return $this->vue;
    }

    public function setVue(?string $vue): static
    {
        $this->vue = $vue;

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
            $sauvegarde->setArticle($this);
        }

        return $this;
    }

    public function removeSauvegarde(Sauvegarde $sauvegarde): static
    {
        if ($this->sauvegardes->removeElement($sauvegarde)) {
            // set the owning side to null (unless already changed)
            if ($sauvegarde->getArticle() === $this) {
                $sauvegarde->setArticle(null);
            }
        }

        return $this;
    }

     /**
     * this code allow to know if the article is saved by the user session
     * @param \App\Entity\User $user
     * @return bool
     */
    public function IsSavedByUser(User $user): bool
    { foreach ($this->sauvegardes as $saved) {
            if($saved->getUser()===$user) return true;
        }
        return false;
    }
    public function getFichier(): ?string
    {
        return $this->fichier;
    }

    public function setFichier(?string $fichier): static
    {
        $this->fichier = $fichier;

        return $this;
    }
    
}
