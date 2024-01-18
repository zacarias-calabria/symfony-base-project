<?php

declare(strict_types=1);

namespace App\Shop\Carts\Application\RemoveProduct;

use App\Shared\Domain\Bus\Command\CommandHandler;
use App\Shop\Carts\Domain\CartId;
use App\Shop\Carts\Domain\CartRepository;

final readonly class RemoveProductCartCommandHandler implements CommandHandler
{
    public function __construct(
        private CartRepository $activeCartRepository,
    ) {
    }

    public function __invoke(RemoveProductCartCommand $command): void
    {
        $cart = $this->activeCartRepository->search(
            id: new CartId(
                value: $command->cartId()
            )
        );
        $cart->removeProductFromCart($command->productId());
        $this->activeCartRepository->save($cart);
    }
}
