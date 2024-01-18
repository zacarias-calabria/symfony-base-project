<?php

declare(strict_types=1);

namespace App\Apps\API\Controller\Cart;

use App\Shared\Infrastructure\Symfony\ApiController;
use App\Shop\Carts\Application\Pay\PayCartCommand;
use App\Shop\Carts\Domain\CartNotFound;
use App\Shop\Carts\Domain\EmptyCartError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
