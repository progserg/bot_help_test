<?php

declare(strict_types=1);

namespace App\Messenger\Message;

class UserMessage
{
    public function __construct(public int $userId, public int $message)
    {
    }

    public function getMessage(): int
    {
        return $this->message;
    }

    public function setMessage(int $message): UserMessage
    {
        $this->message = $message;
        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): UserMessage
    {
        $this->userId = $userId;
        return $this;
    }
}