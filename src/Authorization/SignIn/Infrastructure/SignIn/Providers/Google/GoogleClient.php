<?php

declare(strict_types=1);

namespace App\Authorization\SignIn\Infrastructure\SignIn\Providers\Google;

use App\Authorization\SignIn\Infrastructure\SignIn\Providers\Client as ProviderClient;
use Google\Client;

final class GoogleClient extends Client implements ProviderClient
{
    /**
     * @param array<string, string> $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }
}
