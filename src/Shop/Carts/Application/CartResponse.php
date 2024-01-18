<?php

declare(strict_types=1);

namespace App\Shop\Carts\Application;

use App\Shared\Domain\Bus\Query\Response;
use App\Shared\Domain\Utils\Currencies;
use App\Shop\Carts\Domain\ProductInCart;
use Doctrine\Common\Collections\Collection;

use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;

final readonly class CartResponse implements Response
{
    public function __construct(
        private string $id,
        private string $status,
        private string $createdAt,
        private Collection $productsInCart
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id(),
            'status' => $this->status(),
            'createdAt' => $this->createdAt(),
            'totalPrice' => reduce(
                fn: fn(float $totalPrice, ProductInCart $productInCart): float => Currencies::round(
                    $totalPrice + $productInCart->totalPrice()
                ),
                coll: $this->productsInCart(),
                initial: 0
            ),
            'totalRate' => reduce(
                fn: fn(float $totalRate, ProductInCart $productInCart): float => Currencies::round(
                    $totalRate + $productInCart->totalRate()
                ),
                coll: $this->productsInCart(),
                initial: 0
            ),
            'totalAmount' => reduce(
                fn: fn(float $totalAmount, ProductInCart $productInCart): float => Currencies::round(
                    $totalAmount + $productInCart->totalAmount()
                ),
                coll: $this->productsInCart(),
                initial: 0
            ),
            'productsInCart' => map(
                fn: fn(ProductInCart $productInCart): array => [
                    'productId' => $productInCart->product()->id(),
                    'name' => $productInCart->product()->name(),
                    'quantity' => $productInCart->quantity(),
                    'unitPrice' => $productInCart->unitPrice(),
                    'taxRate' => $productInCart->taxRate(),
                    'unitRate' => $productInCart->unitRate(),
                    'unitAmount' => $productInCart->unitAmount(),
                    'totalPrice' => $productInCart->totalPrice(),
                    'totalRate' => $productInCart->totalRate(),
                    'totalAmount' => $productInCart->totalAmount()
                ],
                coll: $this->productsInCart()
            ),
        ];
    }

    public function id(): string
    {
        return $this->id;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function createdAt(): string
    {
        return $this->createdAt;
    }

    public function productsInCart(): Collection
    {
        return $this->productsInCart;
    }
}
