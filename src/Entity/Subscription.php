<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
class Subscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdat = null;

    /**
     * @var Collection<int, Subcart>
     */
    #[ORM\OneToMany(targetEntity: Subcart::class, mappedBy: 'subscriptionid')]
    private Collection $subcartid;

    public function __construct()
    {
        $this->subcartid = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    /**
     * @return Collection<int, Subcart>
     */
    public function getSubcartid(): Collection
    {
        return $this->subcartid;
    }

    public function addSubcartid(Subcart $subcartid): static
    {
        if (!$this->subcartid->contains($subcartid)) {
            $this->subcartid->add($subcartid);
            $subcartid->setSubscriptionid($this);
        }

        return $this;
    }

    public function removeSubcartid(Subcart $subcartid): static
    {
        if ($this->subcartid->removeElement($subcartid)) {
            // set the owning side to null (unless already changed)
            if ($subcartid->getSubscriptionid() === $this) {
                $subcartid->setSubscriptionid(null);
            }
        }

        return $this;
    }
}
