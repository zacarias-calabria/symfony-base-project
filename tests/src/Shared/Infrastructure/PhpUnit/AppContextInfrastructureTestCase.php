<?php

declare(strict_types=1);

namespace Tests\Techpump\Shared\Infrastructure\PhpUnit;

use Techpump\Apps\API\Kernel;

abstract class AppContextInfrastructureTestCase extends InfrastructureTestCase
{
    protected function kernelClass(): string
    {
        return Kernel::class;
    }
}
