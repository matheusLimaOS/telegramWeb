<?php

namespace App\Entity;

use App\Repository\PerfilCardRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: PerfilCardRepository::class)]
class PerfilCard
{
    public function __construct()
    {
        $this->tips = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\Column(length: 255)]
    private ?string $answer = null;
    
    #[ORM\OneToMany(
        mappedBy: 'card',
        targetEntity: PerfilTip::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $tips;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    public function getTips(): Collection
    {
        return $this->tips;
    }

    public function addTip(PerfilTip $tip): static
    {
        if (!$this->tips->contains($tip)) {
            $this->tips->add($tip);
            $tip->setCard($this);
        }

        return $this;
    }

    public function removeTip(PerfilTip $tip): static
    {
        if ($this->tips->removeElement($tip)) {
            if ($tip->getCard() === $this) {
                $tip->setCard(null);
            }
        }

        return $this;
    }
}
