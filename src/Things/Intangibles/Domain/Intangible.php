<?php

declare(strict_types=1);

namespace App\Things\Intangibles\Domain;

use App\Shared\Domain\AggregateRoot\AggregateRoot;

class Intangible extends AggregateRoot
{
    public function __construct(
        private readonly IntangibleId $id,
        private readonly IntangibleName $name,
        private readonly \DateTimeImmutable $createdAt
    ) {
    }

    public function id(): IntangibleId
    {
        return $this->id;
    }

    public function name(): IntangibleName
    {
        return $this->name;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
