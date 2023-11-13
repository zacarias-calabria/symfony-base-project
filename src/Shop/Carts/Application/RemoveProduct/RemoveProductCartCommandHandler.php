<?php

declare(strict_types=1);

namespace Techpump\Shop\Carts\Application\RemoveProduct;

use Techpump\Shared\Domain\Bus\Command\CommandHandler;
use Techpump\Shop\Carts\Domain\CartId;
use Techpump\Shop\Carts\Domain\CartRepository;

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
