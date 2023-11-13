<?php

declare(strict_types=1);

namespace Techpump\Apps\API\Controller\Cart;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Techpump\Shared\Infrastructure\Symfony\ApiController;
use Techpump\Shop\Carts\Application\Create\CreateCartCommand;
use Techpump\Shop\Carts\Domain\CartAlreadyExistsError;

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
