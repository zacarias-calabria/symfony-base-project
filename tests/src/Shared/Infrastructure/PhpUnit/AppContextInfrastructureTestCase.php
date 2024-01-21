<?php

declare(strict_types=1);

namespace Tests\App\Shared\Infrastructure\PhpUnit;

use App\Apps\API\Kernel;

abstract class AppContextInfrastructureTestCase extends InfrastructureTestCase
{
    #[\Override]
    protected function kernelClass(): string
    {
        return Kernel::class;
    }
}
