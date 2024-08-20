<?php

namespace App\Entity;

use App\Repository\TrainingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrainingRepository::class)]
class Training
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $trainingAt = null;

    #[ORM\ManyToOne(inversedBy: 'trainings')]
    private ?Category $category = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startedAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $endedAt = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $trainingHour = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $trainingDay = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrainingAt(): ?\DateTimeInterface
    {
        return $this->trainingAt;
    }

    public function setTrainingAt(\DateTimeInterface $trainingAt): static
    {
        $this->trainingAt = $trainingAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeInterface $startedAt): static
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getEndedAt(): ?\DateTimeInterface
    {
        return $this->endedAt;
    }

    public function setEndedAt(\DateTimeInterface $endedAt): static
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    public function getTrainingHour(): ?\DateTimeInterface
    {
        return $this->trainingHour;
    }

    public function setTrainingHour(\DateTimeInterface $trainingHour): static
    {
        $this->trainingHour = $trainingHour;

        return $this;
    }

    public function getTrainingDay(): array
    {
        return $this->trainingDay;
    }

    public function setTrainingDay(array $trainingDay): static
    {
        $this->trainingDay = $trainingDay;

        return $this;
    }
}
