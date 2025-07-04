<?php

declare(strict_types=1);

namespace App\Authorization\SignIn\Application\SignUpSSO;

use App\Shared\Domain\Bus\Query\Response;

final readonly class OAuthURLResponse implements Response
{
    public function __construct(public string $location) {}
}
