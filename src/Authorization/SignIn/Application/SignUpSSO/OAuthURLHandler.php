<?php

declare(strict_types=1);

namespace App\Authorization\SignIn\Application\SignUpSSO;

use App\Shared\Domain\Bus\Query\QueryHandler;
use App\Shared\Domain\Bus\Query\Response;

final readonly class OAuthURLHandler implements QueryHandler
{
    public function __invoke(OAuthURL $query): Response
    {
        return new OAuthURLResponse(
            location: 'https://accounts.google.com/o/oauth2/v2/auth?client_id=google-client-id&redirect_uri=http%3A%2F%2Flocalhost%3A3000%2Fsignup%2Fgoogle%2Fcallback&response_type=code&scope=openid%20email%20profile&state=google-state',
        );
    }
}
