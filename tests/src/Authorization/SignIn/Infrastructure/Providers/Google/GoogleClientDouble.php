<?php

declare(strict_types=1);

namespace Tests\App\Authorization\SignIn\Infrastructure\Providers\Google;

use App\Authorization\SignIn\Infrastructure\Providers\Google\Client;

final class GoogleClientDouble implements Client
{
    /**
     * @param string $scope
     * @param array<string, string> $queryParams
     */
    public function createAuthUrl($scope = null, array $queryParams = []): string
    {
        return "https://accounts.google.com/o/oauth2/v2/auth"
            . "?response_type=code"
            . "&access_type=offline"
            . "&client_id=clientIdTest"
            . "&redirect_uri=http%3A%2F%2Flocalhost"
            . "&state=xyz"
            . sprintf("&scope=http%%3A%%2F%%2Fgoogleapis.com%%2Fscope%%2F%1\$s", $scope)
            . "&approval_prompt=auto";
    }
}
