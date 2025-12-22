<?php

namespace App\Entity;

use App\Repository\PerfilTipRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PerfilTipRepository::class)]
class PerfilTip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $tip = null;
    
    #[ORM\ManyToOne(targetEntity: PerfilCard::class, inversedBy: 'tips')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?PerfilCard $card = null;

    #[ORM\Column(type: 'integer')]
    private int $tipOrder;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCard(): ?PerfilCard
    {
        return $this->card;
    }   
    
    public function setCard(PerfilCard $card): ?static
    {
        $this->card = $card;
        return $this;
    }

    public function getTipOrder(): int
    {
        return $this->tipOrder;
    }

    public function setTipOrder(int $tipOrder): static
    {
        $this->tipOrder = $tipOrder;
        return $this;
    }

    public function getTip(): ?string
    {
        return $this->tip;
    }

    public function setTip(string $tip): static
    {
        $this->tip = $tip;

        return $this;
    }
}
