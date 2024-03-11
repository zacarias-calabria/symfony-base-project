<?php

declare(strict_types=1);

namespace Tests\App\Shared\Infrastructure\PhpUnit;

use Doctrine\ORM\EntityManager;
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

    /**
     * @throws \Exception
     */
    protected function clearUnitOfWork(): void
    {
        $this->service(EntityManager::class)->clear();
    }

    protected function eventually(
        callable $fn,
        $totalRetries = 3,
        $timeToWaitOnErrorInSeconds = 1,
        $attempt = 0
    ): void {
        try {
            $fn();
        } catch (\Throwable $error) {
            if ($totalRetries === $attempt) {
                throw $error;
            }

            sleep($timeToWaitOnErrorInSeconds);

            $this->eventually($fn, $totalRetries, $timeToWaitOnErrorInSeconds, $attempt + 1);
        }
    }
}
