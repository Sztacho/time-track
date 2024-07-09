<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\QueryBus;

use App\Application\Shared\Query\QueryHandlerInterface;
use App\Application\Shared\Query\QueryInterface;
use App\Domain\Shared\QueryBusInterface;
use RuntimeException;

final readonly class QueryBus implements QueryBusInterface
{
    /**
     * @var QueryHandlerInterface[]
     */
    private iterable $queryHandlers;

    public function __construct(iterable $queryHandlers)
    {
        $this->queryHandlers = $queryHandlers;
    }

    public function handle(QueryInterface $query): mixed
    {
        foreach ($this->queryHandlers as $queryHandler) {
            if ($queryHandler::class === $query::class . 'Handler') {
                return call_user_func($queryHandler, $query);
            }
        }

        throw new RuntimeException('Query handler for query: ' . $query::class . ' not found.');
    }
}
