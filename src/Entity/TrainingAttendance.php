<?php

namespace App\Entity;

use App\Repository\TrainingAttendanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrainingAttendanceRepository::class)]
class TrainingAttendance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'trainingAttendances')]
    private ?Training $training = null;

    #[ORM\ManyToOne(inversedBy: 'trainingAttendances')]
    private ?Player $player = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $present = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTraining(): ?Training
    {
        return $this->training;
    }

    public function setTraining(?Training $training): static
    {
        $this->training = $training;

        return $this;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): static
    {
        $this->player = $player;

        return $this;
    }

    public function getPresent(): ?int
    {
        return $this->present;
    }

    public function setPresent(int $present): static
    {
        $this->present = $present;

        return $this;
    }
}
