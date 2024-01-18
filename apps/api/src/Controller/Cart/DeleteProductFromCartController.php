<?php

declare(strict_types=1);

namespace App\Apps\API\Controller\Cart;

use App\Shared\Infrastructure\Symfony\ApiController;
use App\Shop\Carts\Application\RemoveProduct\RemoveProductCartCommand;
use App\Shop\Carts\Domain\CartNotFound;
use App\Shop\Carts\Domain\ProductInCartNotFound;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart/{cartId}/product/{productId}', name: 'cart_delete_product', methods: ['DELETE'])]
final class DeleteProductFromCartController extends ApiController
{
    public function __invoke(
        string $cartId,
        string $productId
    ): JsonResponse {
        $this->dispatch(
            new RemoveProductCartCommand(
                cartId: $cartId,
                productId: $productId,
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
            ProductInCartNotFound::class => Response::HTTP_NOT_FOUND
        ];
    }
}
