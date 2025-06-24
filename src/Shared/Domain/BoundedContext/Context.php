<?php

declare(strict_types=1);

namespace App\Shared\Domain\BoundedContext;

final readonly class Context
{
    public function __construct(
        public string $env,
        public bool $debug,
    ) {}
}
