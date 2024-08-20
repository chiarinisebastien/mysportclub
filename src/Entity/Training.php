<?php

namespace App\Entity;

use App\Repository\TrainingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, TrainingAttendance>
     */
    #[ORM\OneToMany(targetEntity: TrainingAttendance::class, mappedBy: 'training')]
    private Collection $player;

    /**
     * @var Collection<int, TrainingAttendance>
     */
    #[ORM\OneToMany(targetEntity: TrainingAttendance::class, mappedBy: 'training')]
    private Collection $trainingAttendances;

    public function __construct()
    {
        $this->player = new ArrayCollection();
        $this->trainingAttendances = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, TrainingAttendance>
     */
    public function getPlayer(): Collection
    {
        return $this->player;
    }

    public function addPlayer(TrainingAttendance $player): static
    {
        if (!$this->player->contains($player)) {
            $this->player->add($player);
            $player->setTraining($this);
        }

        return $this;
    }

    public function removePlayer(TrainingAttendance $player): static
    {
        if ($this->player->removeElement($player)) {
            // set the owning side to null (unless already changed)
            if ($player->getTraining() === $this) {
                $player->setTraining(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TrainingAttendance>
     */
    public function getTrainingAttendances(): Collection
    {
        return $this->trainingAttendances;
    }

    public function addTrainingAttendance(TrainingAttendance $trainingAttendance): static
    {
        if (!$this->trainingAttendances->contains($trainingAttendance)) {
            $this->trainingAttendances->add($trainingAttendance);
            $trainingAttendance->setTraining($this);
        }

        return $this;
    }

    public function removeTrainingAttendance(TrainingAttendance $trainingAttendance): static
    {
        if ($this->trainingAttendances->removeElement($trainingAttendance)) {
            // set the owning side to null (unless already changed)
            if ($trainingAttendance->getTraining() === $this) {
                $trainingAttendance->setTraining(null);
            }
        }

        return $this;
    }
}
