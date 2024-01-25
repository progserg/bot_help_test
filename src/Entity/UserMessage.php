<?php

namespace App\Entity;

use App\Repository\UserMessageRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserMessageRepository::class)]
class UserMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $userId = null;

    #[ORM\Column]
    private ?int $message = null;

    #[ORM\Column]
    private ?DateTime $generated_at = null;

    #[ORM\Column]
    private ?DateTime $processed_at = null;

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): UserMessage
    {
        $this->userId = $userId;
        return $this;
    }

    public function getGeneratedAt(): ?DateTime
    {
        return $this->generated_at;
    }

    public function setGeneratedAt(?DateTime $generated_at): UserMessage
    {
        $this->generated_at = $generated_at;
        return $this;
    }

    public function getProcessedAt(): ?DateTime
    {
        return $this->processed_at;
    }

    public function setProcessedAt(?DateTime $processed_at): UserMessage
    {
        $this->processed_at = $processed_at;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?int
    {
        return $this->message;
    }

    public function setMessage(int $message): static
    {
        $this->message = $message;

        return $this;
    }
}
