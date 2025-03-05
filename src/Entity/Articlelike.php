<?php

namespace App\Entity;

use App\Repository\ArticlelikeRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Article;
use App\Repository\ArticleRepository;

#[ORM\Entity(repositoryClass: ArticlelikeRepository::class)]
class Articlelike
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'Articleid')]
    private ?user $userid = null;

    #[ORM\ManyToOne(inversedBy: 'articlelikes')]
    private ?article $postid = null;

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

    public function getPostid(): ?article
    {
        return $this->postid;
    }

    public function setPostid(?article $postid): static
    {
        $this->postid = $postid;

        return $this;
    }
}
