<?php

declare(strict_types=1);

namespace Tests\App\Shared\Infrastructure\PhpUnit;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class InfrastructureTestCase extends KernelTestCase
{
    abstract protected function kernelClass(): string;

    #[\Override]
    protected function setUp(): void
    {
        $_SERVER['KERNEL_CLASS'] = $this->kernelClass();

        self::bootKernel(['environment' => 'test']);

        parent::setUp();
    }

    /**
     * @throws \Exception
     */
    protected function service(string $id): ?object
    {
        return self::getContainer()->get($id);
    }
}
