<?php

declare(strict_types=1);

namespace Techpump\Apps\API\Controller\Cart;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Techpump\Shared\Infrastructure\Symfony\ApiController;
use Techpump\Shop\Carts\Application\Pay\PayCartCommand;
use Techpump\Shop\Carts\Domain\CartNotFound;
use Techpump\Shop\Carts\Domain\EmptyCartError;

#[Route('/cart/{cartId}/pay', name: 'cart_pay', methods: ['POST'])]
final class PayCartController extends ApiController
{
    public function __invoke(
        string $cartId
    ): JsonResponse {
        $this->dispatch(
            new PayCartCommand(
                cartId: $cartId,
            )
        );
        return new JsonResponse(
            data: [],
            status: Response::HTTP_NO_CONTENT,
        );
    }

    protected function exceptions(): array
    {
        return [
            CartNotFound::class => Response::HTTP_NOT_FOUND,
            EmptyCartError::class => Response::HTTP_BAD_REQUEST
        ];
    }
}
