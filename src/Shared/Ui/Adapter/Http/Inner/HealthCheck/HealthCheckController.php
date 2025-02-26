<?php

declare(strict_types=1);

namespace App\Shared\Ui\Adapter\Http\Inner\HealthCheck;

use App\Shared\Infrastructure\Framework\Symfony\ApiController;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/health-check', methods: ['GET'])]
final class HealthCheckController extends ApiController
{
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(
            [
                'api' => 'ok',
                'timestamp' => new DateTimeImmutable()->getTimestamp(),
            ],
            Response::HTTP_OK,
        );
    }

    #[\Override]
    protected function exceptions(): array
    {
        return [];
    }
}
