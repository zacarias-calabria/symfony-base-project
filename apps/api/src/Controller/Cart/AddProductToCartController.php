<?php

declare(strict_types=1);

namespace Techpump\Apps\API\Controller\Cart;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Techpump\Inventory\Products\Domain\ProductNotFound;
use Techpump\Shared\Infrastructure\Symfony\ApiController;
use Techpump\Shop\Carts\Application\AddProduct\AddProductToCartCommand;
use Techpump\Shop\Carts\Domain\CartNotFound;

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
                quantity: (int) $content['quantity']
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
