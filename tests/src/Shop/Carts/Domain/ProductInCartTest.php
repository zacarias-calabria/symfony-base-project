<?php

declare(strict_types=1);

namespace Tests\App\Shop\Carts\Domain;

use App\Shop\Carts\Domain\ProductInCart;
use PHPUnit\Framework\TestCase;

/**
 * @test
 * @group product
 * @group unit
 */
class ProductInCartTest extends TestCase
{
    /**
     * @test
     * Be A Proper Class
     * be_a_proper_class
     */
    public function itShouldBeAProperClass(): void
    {
        $product = ProductInCartMother::create();
        $this->assertInstanceOf(
            expected: ProductInCart::class,
            actual: $product
        );
    }

    /**
     * @test
     * Update The Product Cart Quantity
     * update_the_product_cart_quantity
     * @group product
     */
    public function itShouldUpdateTheProductCartQuantity(): void
    {
        $product = ProductInCartMother::create();
        $this->assertEquals(
            expected: 1,
            actual: $product->quantity()
        );
        $product->updateQuantity(5);
        $this->assertEquals(
            expected: 5,
            actual: $product->quantity()
        );
    }
}
