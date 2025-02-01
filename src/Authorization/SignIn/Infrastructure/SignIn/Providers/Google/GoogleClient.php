<?php

declare(strict_types=1);

namespace App\Authorization\SignIn\Infrastructure\SignIn\Providers\Google;

use Google\Client;

final class GoogleClient extends Client
{
    /**
     * @param array<string, string> $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }
}
