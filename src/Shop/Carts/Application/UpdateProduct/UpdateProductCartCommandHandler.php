<?php

declare(strict_types=1);

namespace Techpump\Shop\Carts\Application\UpdateProduct;

use Techpump\Shared\Domain\Bus\Command\CommandHandler;
use Techpump\Shop\Carts\Domain\CartId;
use Techpump\Shop\Carts\Domain\CartRepository;
use Techpump\Shop\Carts\Domain\ProductInCartQuantity;

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
