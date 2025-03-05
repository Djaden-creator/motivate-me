<?php

namespace App\Entity;

use App\Repository\GeneratorcodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GeneratorcodeRepository::class)]
class Generatorcode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $codenumber = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    /**
     * @var Collection<int, Motivateur>
     */
    #[ORM\OneToMany(targetEntity: Motivateur::class, mappedBy: 'codeseller')]
    private Collection $motivators;

    public function __construct()
    {
        $this->motivators = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodenumber(): ?string
    {
        return $this->codenumber;
    }

    public function setCodenumber(string $codenumber): static
    {
        $this->codenumber = $codenumber;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }
    public function __toString():string
    {
        return $this->getCodenumber();
    }

    /**
     * @return Collection<int, Motivateur>
     */
    public function getMotivators(): Collection
    {
        return $this->motivators;
    }

    public function addMotivator(Motivateur $motivator): static
    {
        if (!$this->motivators->contains($motivator)) {
            $this->motivators->add($motivator);
            $motivator->setCodeseller($this);
        }

        return $this;
    }

    public function removeMotivator(Motivateur $motivator): static
    {
        if ($this->motivators->removeElement($motivator)) {
            // set the owning side to null (unless already changed)
            if ($motivator->getCodeseller() === $this) {
                $motivator->setCodeseller(null);
            }
        }

        return $this;
    }
}
