<?php

declare(strict_types=1);

namespace Techpump\Shop\Carts\Application\Create;

use Techpump\Shared\Domain\Bus\Command\CommandHandler;
use Techpump\Shop\Carts\Domain\CartAlreadyExistsError;
use Techpump\Shop\Carts\Domain\CartId;
use Techpump\Shop\Carts\Domain\CartNotFound;
use Techpump\Shop\Carts\Domain\CartRepository;

final readonly class CreateCartCommandHandler implements CommandHandler
{
    public function __construct(
        private CartRepository $cartRepository
    ) {
    }

    public function __invoke(CreateCartCommand $command): void
    {
        try {
            $this->cartRepository->search(
                id: new CartId(
                    value: $command->id()
                )
            );
            throw new CartAlreadyExistsError();
        } catch (CartNotFound) {
            $this->cartRepository->save(
                cart: (new CartCreator())(
                    id: new CartId(
                        value: $command->id()
                    )
                )
            );
        }
    }
}
