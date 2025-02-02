<?php

declare(strict_types=1);

namespace App\Authorization\SignIn\Infrastructure\SignIn\Providers\Google;

use App\Authorization\SignIn\Domain\Authorizer;
use App\Authorization\SignIn\Domain\SignInResponse;
use App\Authorization\SignIn\Infrastructure\SignIn\Providers\Client;
use Symfony\Component\HttpFoundation\Response;

final readonly class GoogleAuthorizer implements Authorizer
{

    public function __construct(
        private Client $providerClient
    ) {
    }

    public function signIn(): SignInResponse
    {
        return new HTTPSignInResponse(
            content: null,
            statusCode: Response::HTTP_FOUND,
            headers: ['Location' => $this->providerClient->createAuthUrl()]
        );
    }
}
