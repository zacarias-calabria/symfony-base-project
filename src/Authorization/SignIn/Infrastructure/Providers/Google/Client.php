<?php

namespace App\Authorization\SignIn\Infrastructure\Providers\Google;

interface Client
{
    // @phpstan-ignore-next-line
    public function createAuthUrl($scope = null, array $queryParams = []);
}
