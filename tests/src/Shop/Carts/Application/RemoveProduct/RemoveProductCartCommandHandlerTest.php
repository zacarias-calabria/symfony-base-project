<?php

declare(strict_types=1);

namespace Tests\Techpump\Shop\Carts\Application\RemoveProduct;

use PHPUnit\Framework\TestCase;
use Techpump\Shared\Domain\Bus\Command\CommandHandler;
use Techpump\Shop\Carts\Application\RemoveProduct\RemoveProductCartCommand;
use Techpump\Shop\Carts\Application\RemoveProduct\RemoveProductCartCommandHandler;
use Techpump\Shop\Carts\Domain\CartId;
use Techpump\Shop\Carts\Domain\CartNotFound;
use Techpump\Shop\Carts\Domain\ProductInCart;
use Techpump\Shop\Carts\Domain\ProductInCartNotFound;
use Techpump\Shop\Carts\Infrastructure\Persistence\InMemoryActiveCartRepository;
use Tests\Techpump\Inventory\Products\Domain\ProductMother;
use Tests\Techpump\Shop\Carts\Domain\CartMother;

/**
 * @test
 * @group remove_product_cart_command_handler
 * @group unit
 */
class RemoveProductCartCommandHandlerTest extends TestCase
{
    private const EXISTING_CART_ID = 'cddf970c-d7b8-4d2b-9fcd-3f9644de27d6';
    private const EXISTING_PRODUCT_ID = '7e912f83-b3cd-42c6-ab05-579a841b8b1c';
    private const EXISTING_NOT_IN_CART_PRODUCT_ID = 'def54ed5-dce7-4985-991d-1de1824ae625';

    private RemoveProductCartCommandHandler $handler;
    private InMemoryActiveCartRepository $cartRepository;

    protected function setUp(): void
    {
        $this->cartRepository = new InMemoryActiveCartRepository([
            self::EXISTING_CART_ID => CartMother::create(
                id: new CartId(self::EXISTING_CART_ID)
            ),
        ]);
        $this->handler = new RemoveProductCartCommandHandler(
            activeCartRepository: $this->cartRepository
        );
    }

    /**
     * @test
     * Be A Proper Class
     * be_a_proper_class
     */
    public function itShouldBeAProperClass(): void
    {
        $this->assertInstanceOf(
            expected: RemoveProductCartCommandHandler::class,
            actual: $this->handler
        );
        $this->assertInstanceOf(
            expected: CommandHandler::class,
            actual: $this->handler
        );
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
            new RemoveProductCartCommand(
                cartId: '6e35c1a5-4b5c-41ce-bffe-78efb5bd0823',
                productId: 'ec92351f-8b69-4120-8cb0-8dcb080a4e73',
            )
        );
    }


    /**
     * @test
     * Throw A Product In Cart Not Found Exception If An Existing Cart Dont Contains An Existing Product
     * throw_a_product_in_cart_not_found_exception_if_an_existing_cart_dont_contains_an_existing_product
     */
    public function itShouldThrowAProductInCartNotFoundExceptionIfAnExistingCartDontContainsAnExistingProduct(): void
    {
        $this->expectException(ProductInCartNotFound::class);
        (
        $this->handler
        )(
            new RemoveProductCartCommand(
                cartId: self::EXISTING_CART_ID,
                productId: self::EXISTING_NOT_IN_CART_PRODUCT_ID,
            )
        );
    }

    /**
     * @test
     * Remove A Product From The Cart
     * remove_a_product_from_the_cart
     */
    public function itShouldRemoveAProductFromTheCart(): void
    {
        $cart = $this->cartRepository->search(
            id: new CartId(
                value: self::EXISTING_CART_ID
            )
        );
        $product = ProductMother::create(self::EXISTING_PRODUCT_ID);
        $cart->addProductToCart(
            productInCart: ProductInCart::create(
                cart: $cart,
                product: $product,
                unitPrice: $product->price(),
                taxRate: $product->taxRate(),
                quantity: 2
            )
        );
        $this->cartRepository->save($cart);
        $this->assertCount(
            expectedCount: 1,
            haystack: $cart->productsInCart()
        );
        (
        $this->handler
        )(
            new RemoveProductCartCommand(
                cartId: self::EXISTING_CART_ID,
                productId: self::EXISTING_PRODUCT_ID,
            )
        );
        $this->assertCount(
            expectedCount: 0,
            haystack: $cart->productsInCart()
        );
    }
}
