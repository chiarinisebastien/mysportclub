<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'players')]
    private Collection $category;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $birthdate = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'player')]
    private Collection $users;

    /**
     * @var Collection<int, TrainingAttendance>
     */
    #[ORM\OneToMany(targetEntity: TrainingAttendance::class, mappedBy: 'player')]
    private Collection $trainingAttendances;

    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->trainingAttendances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->category->contains($category)) {
            $this->category->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->category->removeElement($category);

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): static
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addPlayer($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removePlayer($this);
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
            $trainingAttendance->setPlayer($this);
        }

        return $this;
    }

    public function removeTrainingAttendance(TrainingAttendance $trainingAttendance): static
    {
        if ($this->trainingAttendances->removeElement($trainingAttendance)) {
            // set the owning side to null (unless already changed)
            if ($trainingAttendance->getPlayer() === $this) {
                $trainingAttendance->setPlayer(null);
            }
        }

        return $this;
    }
}
