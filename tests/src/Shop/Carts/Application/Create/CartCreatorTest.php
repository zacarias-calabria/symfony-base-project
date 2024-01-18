<?php

declare(strict_types=1);

namespace Tests\App\Shop\Carts\Application\Create;

use App\Shop\Carts\Application\Create\CartCreator;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Tests\App\Shop\Carts\Domain\CartIdMother;

/**
 * @test
 * @group cart_creator
 * @group unit
 */
class CartCreatorTest extends TestCase
{
    /**
     * @test
     * Create A Suitable Cart
     * create_a_suitable_cart
     */
    public function itShouldCreateASuitableCart(): void
    {
        $cartId = CartIdMother::create();
        $cartCreator = new CartCreator();
        $cart = ($cartCreator)($cartId);
        $this->assertEquals(
            expected: $cartId->value(),
            actual: $cart->id()
        );
        $this->assertEquals(
            expected: (new DateTimeImmutable())->format('Y-m-d'),
            actual: ($cart->createdAt())->format('Y-m-d')
        );
        $this->assertEquals(
            expected: 'active',
            actual: $cart->status()
        );
    }
}
