<?php

declare(strict_types=1);

namespace App\Apps\API\Controller\Cart;

use App\Shared\Infrastructure\Symfony\ApiController;
use App\Shop\Carts\Application\UpdateProduct\UpdateProductCartCommand;
use App\Shop\Carts\Domain\CartNotFound;
use App\Shop\Carts\Domain\ProductInCartNotFound;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart/{id}/products/', name: 'cart_update_product', methods: ['PATCH'])]
#[Route('/cart/{id}/products', name: 'cart_update_product_fallback', methods: ['PATCH'])]
final class UpdateProductCartController extends ApiController
{
    public function __invoke(string $id, Request $request): JsonResponse
    {
        $content = $request->toArray();
        $this->dispatch(
            new UpdateProductCartCommand(
                cartId: $id,
                productId: $content['productId'],
                quantity: (int)$content['quantity']
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
