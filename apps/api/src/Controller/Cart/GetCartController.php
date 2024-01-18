<?php

declare(strict_types=1);

namespace App\Apps\API\Controller\Cart;

use App\Shared\Infrastructure\Symfony\ApiController;
use App\Shop\Carts\Application\Get\GetCartQuery;
use App\Shop\Carts\Domain\CartNotFound;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart/{id}', name: 'cart_get', methods: ['GET'])]
final class GetCartController extends ApiController
{
    public function __invoke(string $id): JsonResponse
    {
        $response = $this->ask(
            query: new GetCartQuery(
                id: $id
            )
        );

        return new JsonResponse(
            data: $response->toArray(),
            status: Response::HTTP_OK,
        );
    }

    protected function exceptions(): array
    {
        return [
            CartNotFound::class => Response::HTTP_NOT_FOUND
        ];
    }
}

