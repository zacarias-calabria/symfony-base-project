<?php

declare(strict_types=1);

namespace Techpump\Shop\Carts\Domain;

use DateTimeImmutable;
use Techpump\Inventory\Products\Domain\Product;
use Techpump\Shared\Domain\Utils\Currencies;

class ProductInCart
{
    private string $id;
    private function __construct(
        private readonly Cart $cart,
        private readonly Product $product,
        private readonly float $unitPrice,
        private readonly float $taxRate,
        private int $quantity,
        private float $unitRate,
        private float $unitAmount,
        private float $totalPrice,
        private float $totalRate,
        private float $totalAmount,
        private readonly DateTimeImmutable $addedAt,
    ) {
    }

    public static function create(
        Cart $cart,
        Product $product,
        float $unitPrice,
        float $taxRate,
        int $quantity,
    ): self {
        $unitRate = Currencies::calculateRateFromAmount(amount:$unitPrice, taxRate:$taxRate);
        $unitAmount = Currencies::round(num: $unitPrice + $unitRate);
        $totalPrice = Currencies::round(num: $unitPrice * $quantity);
        $totalRate = Currencies::round(num: $unitRate * $quantity);
        $totalAmount = Currencies::round(num: $unitAmount * $quantity);
        return new self(
            cart: $cart,
            product: $product,
            unitPrice: $unitPrice,
            taxRate: $taxRate,
            quantity: $quantity,
            unitRate: $unitRate,
            unitAmount: $unitAmount,
            totalPrice: $totalPrice,
            totalRate: $totalRate,
            totalAmount: $totalAmount,
            addedAt: new DateTimeImmutable(),
        );
    }

    public function cart(): Cart
    {
        return $this->cart;
    }

    public function product(): Product
    {
        return $this->product;
    }

    public function unitPrice(): float
    {
        return $this->unitPrice;
    }

    public function taxRate(): float
    {
        return $this->taxRate;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function unitRate(): float
    {
        return $this->unitRate;
    }

    public function unitAmount(): float
    {
        return $this->unitAmount;
    }

    public function totalRate(): float
    {
        return $this->totalRate;
    }

    public function totalAmount(): float
    {
        return $this->totalAmount;
    }

    public function totalPrice(): float
    {
        return $this->totalPrice;
    }

    public function addedAt(): DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function updateQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
        $this->updateTotalPrices();
    }

    private function updateTotalPrices(): void
    {
        $this->totalPrice = Currencies::round(num: $this->unitPrice * $this->quantity);
        $this->totalRate = Currencies::round(num: $this->unitRate * $this->quantity);
        $this->totalAmount = Currencies::round(num: $this->unitAmount * $this->quantity);
    }
}
