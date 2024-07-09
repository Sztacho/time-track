<?php

declare(strict_types=1);

namespace App\Domain\Shared;


use App\Application\Shared\Query\QueryInterface;

interface QueryBusInterface
{
    public function handle(QueryInterface $query): mixed;
}