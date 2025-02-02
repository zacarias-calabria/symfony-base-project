<?php

namespace Tests\App\Shared\Infrastructure\PhpUnit;

use App\Apps\API\Kernel;

class ApiInfrastructureTestCase extends InfrastructureTestCase
{

    protected function kernelClass(): string
    {
        return Kernel::class;
    }
}