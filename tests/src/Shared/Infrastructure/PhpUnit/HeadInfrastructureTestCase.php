<?php

namespace Tests\App\Shared\Infrastructure\PhpUnit;

use App\Apps\Head\Kernel;

class HeadInfrastructureTestCase extends InfrastructureTestCase
{

    protected function kernelClass(): string
    {
        return Kernel::class;
    }
}