<?php

declare(strict_types=1);

namespace Techpump\Shared\Domain\Bus\Query;

interface Response {
    public function toArray(): array;
}
