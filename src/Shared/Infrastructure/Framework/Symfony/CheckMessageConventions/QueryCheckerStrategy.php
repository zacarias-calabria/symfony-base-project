<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Framework\Symfony\CheckMessageConventions;

use App\Shared\Domain\Bus\Event\Event;

final class QueryCheckerStrategy extends AbstractCheckerStrategy
{
    public function getClassName(): string
    {
        return Event::class;
    }

    protected function getSuffix(): string
    {
        return 'Query';
    }
}
