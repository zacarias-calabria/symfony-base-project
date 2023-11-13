<?php

declare(strict_types=1);

namespace Techpump\Shop\Carts\Application\Pay;

use Techpump\Shared\Domain\Bus\Command\CommandHandler;
use Techpump\Shop\Carts\Domain\CartId;
use Techpump\Shop\Carts\Domain\CartRepository;
use Techpump\Shop\Carts\Domain\EmptyCartError;

final class PayCartCommandHandler implements CommandHandler
{
    public function __construct(
        private CartRepository $activeCartRepository,
    ) {
    }

    public function __invoke(PayCartCommand $command): void
    {
        $cart = $this->activeCartRepository->search(
            id: new CartId(
                value: $command->cartId()
            )
        );
        if ($cart->isEmpty()) {
            throw new EmptyCartError('Can\'t pay for an empty cart.');
        }
        $cart->hasBeenPaidSuccessfully();
        $this->activeCartRepository->save($cart);
    }
}
