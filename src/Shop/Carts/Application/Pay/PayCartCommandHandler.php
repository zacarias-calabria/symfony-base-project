<?php

declare(strict_types=1);

namespace App\Shop\Carts\Application\Pay;

use App\Shared\Domain\Bus\Command\CommandHandler;
use App\Shop\Carts\Domain\CartId;
use App\Shop\Carts\Domain\CartRepository;
use App\Shop\Carts\Domain\EmptyCartError;

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
