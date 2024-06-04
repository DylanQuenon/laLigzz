<?php

namespace App\Entity;

use App\Repository\MatchesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MatchesRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Matches
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'matches')]
    private ?Team $homeTeam = null;

    #[ORM\Column]
    private ?int $homeTeamGoals = null;

    #[ORM\ManyToOne(inversedBy: 'matches')]
    private ?Team $awayTeam = null;

    #[ORM\Column(length: 255)]
    private ?string $journee = null;

    #[ORM\Column(length: 255)]
    private ?string $stadium = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\Range(
    min : "2023-08-01",
    max : "2024-06-30",
    notInRangeMessage :"La date doit être postérieure au 1er août 2023 et inférieure au 30 juin 2024",
)]

    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?int $awayTeamGoals = null;

      /**
     * Permet d'intialiser le slug automatiquement si on ne le donne pas
     *
     * @return void
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeStadium(): void
    {
        if(empty($this->stadium))
        {
            $this->stadium = $this->homeTeam->getStadium();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHomeTeam(): ?Team
    {
        return $this->homeTeam;
    }

    public function setHomeTeam(?Team $homeTeam): static
    {
        $this->homeTeam = $homeTeam;

        return $this;
    }

    public function getHomeTeamGoals(): ?int
    {
        return $this->homeTeamGoals;
    }

    public function setHomeTeamGoals(int $homeTeamGoals): static
    {
        $this->homeTeamGoals = $homeTeamGoals;

        return $this;
    }

    public function getAwayTeam(): ?Team
    {
        return $this->awayTeam;
    }

    public function setAwayTeam(?Team $awayTeam): static
    {
        $this->awayTeam = $awayTeam;

        return $this;
    }

    public function getJournee(): ?string
    {
        return $this->journee;
    }

    public function setJournee(string $journee): static
    {
        $this->journee = $journee;

        return $this;
    }

    public function getStadium(): ?string
    {
        return $this->stadium;
    }

    public function setStadium(string $stadium): static
    {
        $this->stadium = $stadium;

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

    public function getAwayTeamGoals(): ?int
    {
        return $this->awayTeamGoals;
    }

    public function setAwayTeamGoals(int $awayTeamGoals): static
    {
        $this->awayTeamGoals = $awayTeamGoals;

        return $this;
    }
    
}
