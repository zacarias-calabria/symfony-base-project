<?php

declare(strict_types=1);

namespace Techpump\Apps\API\Controller\HealthCheck;

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Techpump\Shared\Infrastructure\Symfony\ApiController;

#[Route('/health-check', name: 'health_check_get', methods: ['GET'])]
final class HealthCheckGetController extends ApiController
{
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(
            [
                'api' => 'ok',
                'timestamp' => (new DateTimeImmutable())->getTimestamp(),
            ],
            Response::HTTP_OK
        );
    }

    protected function exceptions(): array
    {
        return [];
    }
}
