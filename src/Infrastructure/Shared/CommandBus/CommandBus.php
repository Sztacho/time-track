<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\CommandBus;

use App\Application\Shared\Command\CommandInterface;
use App\Application\Shared\Command\CommandHandlerInterface;
use App\Domain\Shared\CommandBusInterface;
use RuntimeException;

class CommandBus implements CommandBusInterface
{
    /** @var CommandHandlerInterface[] */
    private iterable $commandHandlers;

    public function __construct(iterable $commandHandlers)
    {
        $this->commandHandlers = $commandHandlers;
    }

    public function handle(CommandInterface $command): mixed
    {
        foreach ($this->commandHandlers as $commandHandler) {
            if ($commandHandler::class === $command::class . 'Handler') {
                return call_user_func($commandHandler, $command);
            }
        }

        throw new RuntimeException('Handler for command: ' . $command::class . ' not found');
    }
}