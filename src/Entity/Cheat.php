<?php

namespace App\Entity;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CheatRepository;

#[ORM\Entity(repositoryClass: CheatRepository::class)]
class Cheat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $cheatAt = null;

    #[ORM\ManyToOne(inversedBy: 'cheats')]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $message = null;

    public function __construct()
    {
        $now = new DateTime();
        $this->cheatAt = new DateTimeImmutable($now->format('Y-m-d H:i:s'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCheatAt(): ?\DateTimeImmutable
    {
        return $this->cheatAt;
    }

    public function setCheatAt(\DateTimeImmutable $cheatAt): static
    {
        $this->cheatAt = $cheatAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }
}
