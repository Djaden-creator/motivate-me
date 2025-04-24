<?php

namespace App\Entity;

use App\Repository\GroupsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupsRepository::class)]
#[ORM\Table(name: '`groups`')]
class Groups
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'groupsid')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userid = null;

    #[ORM\Column(length: 255)]
    private ?string $groupname = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $regle = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    /**
     * @var Collection<int, Addingroup>
     */
    #[ORM\OneToMany(targetEntity: Addingroup::class, mappedBy: 'groupid')]
    private Collection $addingroups;

    /**
     * @var Collection<int, Shareingroup>
     */
    #[ORM\OneToMany(targetEntity: Shareingroup::class, mappedBy: 'groupid')]
    private Collection $shareingroups;


    public function __construct()
    {
        $this->addingroups = new ArrayCollection();
        $this->shareingroups = new ArrayCollection();
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

    public function getGroupname(): ?string
    {
        return $this->groupname;
    }

    public function setGroupname(string $groupname): static
    {
        $this->groupname = $groupname;

        return $this;
    }

    public function getRegle(): ?string
    {
        return $this->regle;
    }

    public function setRegle(string $regle): static
    {
        $this->regle = $regle;

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
     * @return Collection<int, Addingroup>
     */
    public function getAddingroups(): Collection
    {
        return $this->addingroups;
    }

    public function addAddingroup(Addingroup $addingroup): static
    {
        if (!$this->addingroups->contains($addingroup)) {
            $this->addingroups->add($addingroup);
            $addingroup->setGroupid($this);
        }

        return $this;
    }

    public function removeAddingroup(Addingroup $addingroup): static
    {
        if ($this->addingroups->removeElement($addingroup)) {
            // set the owning side to null (unless already changed)
            if ($addingroup->getGroupid() === $this) {
                $addingroup->setGroupid(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Shareingroup>
     */
    public function getShareingroups(): Collection
    {
        return $this->shareingroups;
    }

    public function addShareingroup(Shareingroup $shareingroup): static
    {
        if (!$this->shareingroups->contains($shareingroup)) {
            $this->shareingroups->add($shareingroup);
            $shareingroup->setGroupid($this);
        }

        return $this;
    }

    public function removeShareingroup(Shareingroup $shareingroup): static
    {
        if ($this->shareingroups->removeElement($shareingroup)) {
            // set the owning side to null (unless already changed)
            if ($shareingroup->getGroupid() === $this) {
                $shareingroup->setGroupid(null);
            }
        }

        return $this;
    }

}
