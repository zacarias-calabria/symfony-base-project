<?php

declare(strict_types=1);

namespace App\Shop\Carts\Domain;

use App\Shared\Domain\AggregateRoot\AggregateRoot;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use function Lambdish\Phunctional\search;

class Cart extends AggregateRoot
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAID = 'paid';
    private Collection $productsInCart;

    private function __construct(
        private readonly CartId $id,
        private string $status,
        private readonly DateTimeImmutable $createdAt,
    ) {
        $this->productsInCart = new ArrayCollection();
    }

    public static function create(
        CartId $id,
        string $status,
        DateTimeImmutable $createdAt
    ): self {
        return new self(
            id: $id,
            status: $status,
            createdAt: $createdAt,
        );
    }

    public function status(): string
    {
        return $this->status;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function addProductToCart(ProductInCart $productInCart): void
    {
        try {
            $existingProductInCart = $this->productInCartByProductId(productId: $productInCart->product()->id());
            $this->updateProductCartQuantity(
                productId: $productInCart->product()->id(),
                quantity: $existingProductInCart->quantity() + $productInCart->quantity()
            );
        } catch (ProductInCartNotFound) {
            $this->productsInCart->add($productInCart);
        }
    }

    public function productInCartByProductId(string $productId): ProductInCart
    {
        return search(
            fn: fn(ProductInCart $product): bool => $productId === $product->product()->id(),
            coll: $this->productsInCart(),
        )
            ?? throw new ProductInCartNotFound();
    }

    public function id(): CartId
    {
        return $this->id;
    }

    public function productsInCart(): Collection
    {
        return $this->productsInCart;
    }

    public function updateProductCartQuantity(string $productId, int $quantity): void
    {
        $this->productInCartByProductId(
            productId: $productId
        )->updateQuantity($quantity);
    }

    public function removeProductFromCart(string $productId): void
    {
        $this->productsInCart->removeElement(
            element: $this->productInCartByProductId(
                productId: $productId
            )
        );
    }

    public function hasBeenPaidSuccessfully(): void
    {
        $this->status = self::STATUS_PAID;
    }

    public function isEmpty(): bool
    {
        return $this->productsInCart()->isEmpty();
    }
}
