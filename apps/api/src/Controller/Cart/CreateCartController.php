<?php

declare(strict_types=1);

namespace App\Apps\API\Controller\Cart;

use App\Shared\Infrastructure\Symfony\ApiController;
use App\Shop\Carts\Application\Create\CreateCartCommand;
use App\Shop\Carts\Domain\CartAlreadyExistsError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart/{id}', name: 'cart_create', methods: ['PUT'])]
final class CreateCartController extends ApiController
{
    public function __invoke(string $id): JsonResponse
    {
        $this->dispatch(
            new CreateCartCommand(
                id: $id
            )
        );

        return new JsonResponse(
            data: [],
            status: Response::HTTP_CREATED,
        );
    }

    protected function exceptions(): array
    {
        return [
            CartAlreadyExistsError::class => Response::HTTP_BAD_REQUEST
        ];
    }
}
