<?php

namespace App\Entity;

use App\Repository\TipsRevealedRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    uniqueConstraints: [
        new ORM\UniqueConstraint(
            name: 'uniq_user_card_tip',
            columns: ['user_id', 'card_id', 'tip_id']
        )
    ]
)]
class TipsRevealed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: PerfilCard::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?PerfilCard $card = null;

    #[ORM\ManyToOne(targetEntity: PerfilTip::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?PerfilTip $tip = null;    
    
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $guessed = false;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $guess = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $guessRight = false;

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }    
    
    public function setCard(?PerfilCard $card): static
    {
        $this->card = $card;

        return $this;
    }
    
    public function setTip(?PerfilTip $tip): static
    {
        $this->tip = $tip;

        return $this;
    }    
    
    public function getGuess(): ?string
    {
        return $this->guess;
    }       

    public function getGuessRight(): ?bool
    {
        return $this->guessRight;
    }  

    public function getGuessed(): ?bool
    {
        return $this->guessed;
    }       
    
    public function setGuess(?string $guess): static
    {
        $this->guess = $guess;

        return $this;
    }    
    
    public function setGuessed(?bool $guessed): static
    {
        $this->guessed = $guessed;

        return $this;
    }

    public function setGuessRight(?bool $guessRight): static
    {
        $this->guessRight = $guessRight;

        return $this;
    }
}