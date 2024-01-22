<?php

declare(strict_types=1);

namespace App\Apps\API\Controller\HealthCheck;

use App\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/health-check', name: 'health_check_get', methods: ['GET'])]
final class HealthCheckGetController extends ApiController
{
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(
            [
                'api' => 'ok',
                'timestamp' => (new \DateTimeImmutable())->getTimestamp(),
            ],
            Response::HTTP_OK
        );
    }

    #[\Override]
    protected function exceptions(): array
    {
        return [];
    }
}
