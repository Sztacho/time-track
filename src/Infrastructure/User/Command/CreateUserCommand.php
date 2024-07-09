<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Command;

use App\Application\Shared\Command\CommandInterface;
use App\Domain\User\Entity\User;

readonly class CreateUserCommand implements CommandInterface
{
    public function __construct(private User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}