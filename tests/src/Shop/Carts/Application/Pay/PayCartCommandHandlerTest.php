<?php

declare(strict_types=1);

namespace Tests\App\Shop\Carts\Application\Pay;

use App\Shared\Domain\Bus\Command\CommandHandler;
use App\Shop\Carts\Application\Pay\PayCartCommand;
use App\Shop\Carts\Application\Pay\PayCartCommandHandler;
use App\Shop\Carts\Domain\Cart;
use App\Shop\Carts\Domain\CartId;
use App\Shop\Carts\Domain\CartNotFound;
use App\Shop\Carts\Domain\CartRepository;
use App\Shop\Carts\Domain\EmptyCartError;
use App\Shop\Carts\Domain\ProductInCart;
use App\Shop\Carts\Infrastructure\Persistence\InMemoryActiveCartRepository;
use PHPUnit\Framework\TestCase;
use Tests\App\Inventory\Products\Domain\ProductMother;
use Tests\App\Shop\Carts\Domain\CartIdMother;
use Tests\App\Shop\Carts\Domain\CartMother;

/**
 * @test
 * @group pay_cart_command_handler
 * @group cart
 * @group unit
 */
class PayCartCommandHandlerTest extends TestCase
{
    private const EXISTING_CART_ID = 'cddf970c-d7b8-4d2b-9fcd-3f9644de27d6';
    private CartRepository $repository;
    private PayCartCommandHandler $handler;

    /**
     * @test
     * Be A Proper Class
     * be_a_proper_class
     */
    public function itShouldBeAProperClass(): void
    {
        $this->assertInstanceOf(PayCartCommandHandler::class, $this->handler);
        $this->assertInstanceOf(CommandHandler::class, $this->handler);
    }

    /**
     * @test
     * Throw A Not Cart Error If The Cart Doesnt Exists
     * throw_a_not_cart_error_if_the_cart_doesnt_exists
     */
    public function itShouldThrowANotCartErrorIfTheCartDoesntExists(): void
    {
        $this->expectException(CartNotFound::class);
        (
        $this->handler
        )(
            new PayCartCommand(
                cartId: '6e35c1a5-4b5c-41ce-bffe-78efb5bd0823',
            )
        );
    }

    /**
     * @test
     * Throw A Empty Cart Error For Empty Cart
     * throw_a_empty_cart_error_for_empty_cart
     */
    public function itShouldThrowAEmptyCartErrorForEmptyCart(): void
    {
        $this->expectException(EmptyCartError::class);
        (
        $this->handler
        )(
            new PayCartCommand(
                cartId: self::EXISTING_CART_ID,
            )
        );
    }

    /**
     * @test
     * Pay A Cart
     * pay_a_cart
     */
    public function itShouldPayACart(): void
    {
        $cart = $this->repository->search(
            id: CartIdMother::create(
                value: self::EXISTING_CART_ID
            )
        );
        $product = ProductMother::create();
        $cart->addProductToCart(
            productInCart: ProductInCart::create(
                cart: $cart,
                product: $product,
                unitPrice: $product->price(),
                taxRate: $product->taxRate(),
                quantity: 1
            )
        );
        $this->assertEquals(
            expected: Cart::STATUS_ACTIVE,
            actual: $cart->status()
        );
        (
        $this->handler
        )(
            new PayCartCommand(
                cartId: self::EXISTING_CART_ID,
            )
        );
        $this->expectException(CartNotFound::class);
        $this->repository->search(
            id: CartIdMother::create(
                value: self::EXISTING_CART_ID
            )
        );
    }

    protected function setUp(): void
    {
        $this->repository = new InMemoryActiveCartRepository([
            self::EXISTING_CART_ID => CartMother::create(
                id: new CartId(self::EXISTING_CART_ID)
            ),
        ]);
        $this->handler = new PayCartCommandHandler(
            activeCartRepository: $this->repository
        );
    }
}
