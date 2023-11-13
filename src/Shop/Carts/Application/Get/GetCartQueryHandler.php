<?php

declare(strict_types=1);

namespace Techpump\Shop\Carts\Application\Get;

use CodelyTv\Backoffice\Courses\Application\BackofficeCourseResponse;
use CodelyTv\Backoffice\Courses\Domain\BackofficeCourse;
use Techpump\Shared\Domain\Bus\Query\QueryHandler;
use Techpump\Shop\Carts\Application\CartResponse;
use Techpump\Shop\Carts\Domain\CartId;
use Techpump\Shop\Carts\Domain\CartRepository;

final readonly class GetCartQueryHandler implements QueryHandler
{
    public function __construct(
        private CartRepository $cartRepository
    ) {
    }

    public function __invoke(GetCartQuery $query): CartResponse
    {
        $cart = $this->cartRepository->search(
            id: new CartId(
                value: $query->id()
            )
        );
        return new CartResponse(
            id: $cart->id()->value(),
            status: $cart->status(),
            createdAt: $cart->createdAt()->format('Y-m-d H:i:s'),
            productsInCart: $cart->productsInCart()
        );
    }
}
