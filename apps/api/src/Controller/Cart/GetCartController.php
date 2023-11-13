<?php

declare(strict_types=1);

namespace Techpump\Apps\API\Controller\Cart;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Techpump\Shared\Infrastructure\Symfony\ApiController;
use Techpump\Shop\Carts\Application\Get\GetCartQuery;
use Techpump\Shop\Carts\Domain\CartNotFound;

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

