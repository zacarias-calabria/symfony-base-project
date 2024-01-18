<?php

declare(strict_types=1);

namespace Tests\App\Shared\Domain;

use Closure;
use DomainException;

trait TraitInMemoryRepository
{
    private bool $forceThrowAndException = false;

    public function __construct(
        private array $objects = []
    ) {
    }

    abstract protected function getObjectId(object $object): mixed;

    private function forceThrowAndExceptionIfIndicated(): void
    {
        if ($this->forceThrowAndException) {
            throw new DomainException('Abstract in memory repository exception.');
        }
    }

    public function throwAnExceptionOnNextMethodCall(): void
    {
        $this->forceThrowAndException = true;
    }

    protected function persistInMemoryObject(object $object): void
    {
        $this->forceThrowAndExceptionIfIndicated();
        $this->objects[$this->getObjectId($object)] = clone $object;
    }

    protected function findObject(Closure $comparator): ?object
    {
        $this->forceThrowAndExceptionIfIndicated();

        foreach ($this->objects as $object) {
            if ($comparator($object)) {
                return clone $object;
            }
        }

        return null;
    }
}
