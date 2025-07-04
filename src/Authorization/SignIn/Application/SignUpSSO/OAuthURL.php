<?php

declare(strict_types=1);

namespace App\Authorization\SignIn\Application\SignUpSSO;

use App\Shared\Domain\Bus\Query\Query;

/**
 * @extends Query<OAuthURLResponse>
 */
final readonly class OAuthURL implements Query
{
    public function __construct() {}
}
