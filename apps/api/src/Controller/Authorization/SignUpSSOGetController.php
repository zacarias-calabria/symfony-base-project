<?php

declare(strict_types=1);

namespace App\Apps\API\Controller\Authorization;

use App\Shared\Infrastructure\Symfony\ApiController;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/signup/google', name: 'auth_signup', methods: ['GET'])]
final class SignUpSSOGetController extends ApiController
{
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(
            [
                'api' => 'ok',
                'timestamp' => new DateTimeImmutable()->getTimestamp(),
            ],
            Response::HTTP_FOUND,
            [
                'Location' => 'https://accounts.google.com/o/oauth2/v2/auth?client_id=google-client-id&redirect_uri=http%3A%2F%2Flocalhost%3A3000%2Fsignup%2Fgoogle%2Fcallback&response_type=code&scope=openid%20email%20profile&state=google-state',
            ]
        );
    }

    #[\Override]
    protected function exceptions(): array
    {
        return [];
    }
}
