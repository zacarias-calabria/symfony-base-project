<?php

declare(strict_types=1);

namespace App\Shop\Carts\Application\UpdateProduct;

use App\Shared\Domain\Bus\Command\CommandHandler;
use App\Shop\Carts\Domain\CartId;
use App\Shop\Carts\Domain\CartRepository;
use App\Shop\Carts\Domain\ProductInCartQuantity;

final readonly class UpdateProductCartCommandHandler implements CommandHandler
{
    public function __construct(
        private CartRepository $activeCartRepository
    ) {
    }

    public function __invoke(UpdateProductCartCommand $command): void
    {
        $quantity = new ProductInCartQuantity($command->quantity());
        $cart = $this->activeCartRepository->search(
            id: new CartId(
                value: $command->cartId()
            )
        );
        $cart->updateProductCartQuantity($command->productId(), $quantity->value());
        $this->activeCartRepository->save($cart);
    }
}
