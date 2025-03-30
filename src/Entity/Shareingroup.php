<?php

namespace App\Entity;

use App\Repository\ShareingroupRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

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
}
