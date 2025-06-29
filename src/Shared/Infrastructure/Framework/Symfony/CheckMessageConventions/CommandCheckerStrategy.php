<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Framework\Symfony\CheckMessageConventions;

use App\Shared\Domain\Bus\Command\Command;

final class CommandCheckerStrategy extends AbstractCheckerStrategy
{
    public function getClassName(): string
    {
        return Command::class;
    }

    protected function getSuffix(): string
    {
        return 'Command';
    }
}
