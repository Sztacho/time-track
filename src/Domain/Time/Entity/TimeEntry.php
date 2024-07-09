<?php

declare(strict_types=1);

namespace App\Domain\Time\Entity;

use App\Domain\Task\Entity\Task;
use App\Domain\User\Entity\User;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class TimeEntry implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Task::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Task $task;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    private DateTimeInterface $startTime;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $endTime = null;

    #[ORM\Column(type: 'integer')]
    private int $duration;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $createdAt;

    public function __construct(Task $task, User $user, DateTimeInterface $startTime, ?DateTimeInterface $endTime = null, string $description = null)
    {
        $this->task = $task;
        $this->user = $user;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->description = $description;
        $this->duration = $endTime ? $endTime->getTimestamp() - $startTime->getTimestamp() : 0;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function setTask(Task $task): self
    {
        $this->task = $task;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getStartTime(): DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): ?DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'task' => $this->task,
            'startTime' => $this->startTime->getTimestamp(),
            'endTime' => $this->endTime?->getTimestamp(),
            'duration' => $this->duration,
            'description' => $this->description,
            'createdAt' => $this->createdAt->getTimestamp(),
        ];
    }
}
