<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use App\Application\Shared\Command\CommandInterface;

interface CommandBusInterface
{
    public function handle(CommandInterface $command): mixed;
}