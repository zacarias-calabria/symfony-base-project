<?php

declare(strict_types=1);

namespace Tests\App\Shared\Infrastructure\PhpUnit;

use App\Apps\API\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class ApiAppWebTestCase extends WebTestCase
{
    #[\Override]
    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }
}
