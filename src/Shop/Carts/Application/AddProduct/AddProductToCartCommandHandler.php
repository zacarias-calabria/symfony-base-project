<?php

declare(strict_types=1);

namespace App\Shop\Carts\Application\AddProduct;

use App\Inventory\Products\Domain\ProductRepository;
use App\Shared\Domain\Bus\Command\CommandHandler;
use App\Shop\Carts\Domain\CartId;
use App\Shop\Carts\Domain\CartRepository;
use App\Shop\Carts\Domain\ProductInCart;
use App\Shop\Carts\Domain\ProductInCartQuantity;

final readonly class AddProductToCartCommandHandler implements CommandHandler
{
    public function __construct(
        private CartRepository $activeCartRepository,
        private ProductRepository $productRepository
    ) {
    }

    public function __invoke(AddProductToCartCommand $command): void
    {
        $quantity = new ProductInCartQuantity($command->quantity());
        $cart = $this->activeCartRepository->search(
            id: new CartId(
                value: $command->cartId()
            )
        );
        $product = $this->productRepository->search(
            id: $command->productId()
        );
        $cart->addProductToCart(
            productInCart: ProductInCart::create(
                cart: $cart,
                product: $product,
                unitPrice: $product->price(),
                taxRate: $product->taxRate(),
                quantity: $quantity->value(),
            )
        );
        $this->activeCartRepository->save(cart: $cart);
    }
}
