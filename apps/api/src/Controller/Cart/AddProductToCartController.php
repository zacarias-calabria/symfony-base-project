<?php

declare(strict_types=1);

namespace App\Apps\API\Controller\Cart;

use App\Inventory\Products\Domain\ProductNotFound;
use App\Shared\Infrastructure\Symfony\ApiController;
use App\Shop\Carts\Application\AddProduct\AddProductToCartCommand;
use App\Shop\Carts\Domain\CartNotFound;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart/{id}/products/', name: 'cart_add_product', methods: ['POST'])]
#[Route('/cart/{id}/products', name: 'cart_add_product_fallback', methods: ['POST'])]
final class AddProductToCartController extends ApiController
{
    public function __invoke(string $id, Request $request): JsonResponse
    {
        $content = $request->toArray();
        $this->dispatch(
            new AddProductToCartCommand(
                cartId: $id,
                productId: $content['productId'],
                quantity: (int)$content['quantity']
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
            CartNotFound::class => Response::HTTP_NOT_FOUND,
            ProductNotFound::class => Response::HTTP_NOT_FOUND
        ];
    }
}
