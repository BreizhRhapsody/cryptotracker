<?php

namespace App\Entity;

use App\Repository\SaveOfJourneyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SaveOfJourneyRepository::class)]
class SaveOfJourney
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'date')]
    private $date;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $profit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getProfit(): ?int
    {
        return $this->profit;
    }

    public function setProfit(?int $profit): self
    {
        $this->profit = $profit;

        return $this;
    }
}
