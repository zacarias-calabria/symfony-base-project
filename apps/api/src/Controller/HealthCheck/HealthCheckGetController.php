<?php

declare(strict_types=1);

namespace Techpump\Apps\API\Controller\HealthCheck;

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/health-check', name: 'health-check_get', methods: ['GET'])]
final class HealthCheckGetController
{
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(
            [
                'api' => 'ok',
                'timestamp' => (new DateTimeImmutable())->getTimestamp(),
            ]
        );
    }
}
