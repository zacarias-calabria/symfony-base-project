<?php

declare(strict_types=1);

namespace App\Shop\Carts\Application\Get;

use App\Shared\Domain\Bus\Query\QueryHandler;
use App\Shop\Carts\Application\CartResponse;
use App\Shop\Carts\Domain\CartId;
use App\Shop\Carts\Domain\CartRepository;
use CodelyTv\Backoffice\Courses\Application\BackofficeCourseResponse;
use CodelyTv\Backoffice\Courses\Domain\BackofficeCourse;

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
