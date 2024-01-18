<?php

declare(strict_types=1);

namespace Tests\App\Shop\Carts\Application\AddProduct;

use App\Inventory\Products\Domain\Product;
use App\Inventory\Products\Domain\ProductNotFound;
use App\Inventory\Products\Infrastructure\Persistence\InMemoryProductRepository;
use App\Shared\Domain\Bus\Command\CommandHandler;
use App\Shop\Carts\Application\AddProduct\AddProductToCartCommand;
use App\Shop\Carts\Application\AddProduct\AddProductToCartCommandHandler;
use App\Shop\Carts\Domain\Cart;
use App\Shop\Carts\Domain\CartId;
use App\Shop\Carts\Domain\CartNotFound;
use App\Shop\Carts\Domain\InsufficientQuantityProductsError;
use App\Shop\Carts\Domain\ProductInCart;
use App\Shop\Carts\Infrastructure\Persistence\InMemoryActiveCartRepository;
use PHPUnit\Framework\TestCase;
use Tests\App\Inventory\Products\Domain\ProductMother;
use Tests\App\Shop\Carts\Domain\CartMother;

use function Lambdish\Phunctional\first;

/**
 * @test
 * @group add_product_to_cart_command_handler
 * @group unit
 */
class AddProductToCartCommandHandlerTest extends TestCase
{
    private const EXISTING_CART_ID = 'cddf970c-d7b8-4d2b-9fcd-3f9644de27d6';
    private const EXISTING_PRODUCT_ID = '7e912f83-b3cd-42c6-ab05-579a841b8b1c';
    private AddProductToCartCommandHandler $handler;
    private InMemoryActiveCartRepository $cartRepository;
    private InMemoryProductRepository $productRepository;

    /**
     * @test
     * Be A Proper Class
     * be_a_proper_class
     */
    public function itShouldBeAProperClass(): void
    {
        $this->assertInstanceOf(AddProductToCartCommandHandler::class, $this->handler);
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
            new AddProductToCartCommand(
                cartId: '6e35c1a5-4b5c-41ce-bffe-78efb5bd0823',
                productId: 'ec92351f-8b69-4120-8cb0-8dcb080a4e73',
                quantity: 1
            )
        );
    }

    /**
     * @test
     * Throw A Product Not Found Error If The Product Doesnt Exists
     * throw_a_product_not_found_error_if_the_product_doesnt_exists
     */
    public function itShouldThrowAProductNotFoundErrorIfTheProductDoesntExists(): void
    {
        $this->expectException(ProductNotFound::class);
        (
        $this->handler
        )(
            new AddProductToCartCommand(
                cartId: self::EXISTING_CART_ID,
                productId: 'ec92351f-8b69-4120-8cb0-8dcb080a4e73',
                quantity: 1
            )
        );
    }

    /**
     * @test
     * Throw An Insufficient Quantity Products For Less Than One Quantity
     * throw_an_insufficient_quantity_products_for_less_than_one_quantity
     */
    public function itShouldThrowAnInsufficientQuantityProductsForLessThanOneQuantity(): void
    {
        $this->expectException(InsufficientQuantityProductsError::class);
        (
        $this->handler
        )(
            new AddProductToCartCommand(
                cartId: self::EXISTING_CART_ID,
                productId: self::EXISTING_PRODUCT_ID,
                quantity: 0
            )
        );
    }

    /**
     * @test
     * Add A New Product To Cart
     * add_a_new_product_to_cart
     */
    public function itShouldAddANewProductToCart(): void
    {
        $cart = $this->getAValidCart();
        $product = $this->getAValidProduct();
        ($this->handler)(
            new AddProductToCartCommand(
                cartId: $cart->id()->value(),
                productId: $product->id(),
                quantity: 1
            )
        );
        $firstCartProductFound = $this->getTheFirstProductFromTheCart($cart);
        $this->assertCount(
            expectedCount: 1,
            haystack: $cart->productsInCart()
        );
        $this->assertEquals(
            expected: [
                $cart,
                $product,
                $product->price(),
                $product->taxRate(),
                1,
            ],
            actual: [
                $firstCartProductFound->cart(),
                $firstCartProductFound->product(),
                $firstCartProductFound->unitPrice(),
                $firstCartProductFound->taxRate(),
                $firstCartProductFound->quantity(),
            ]
        );
    }

    private function getAValidCart(): Cart
    {
        return $this->cartRepository->search(
            id: new CartId(
                value: self::EXISTING_CART_ID
            )
        );
    }

    private function getAValidProduct(): Product
    {
        return $this->productRepository->search(
            id: self::EXISTING_PRODUCT_ID
        );
    }

    private function getTheFirstProductFromTheCart(Cart $cart): ProductInCart
    {
        return first($cart->productsInCart());
    }

    /**
     * @test
     * Make Proper Prices When Add A Product
     * make_proper_prices_when_add_a_product
     */
    public function itShouldMakeProperPricesWhenAddAProduct(): void
    {
        $cart = $this->getAValidCart();
        $product = $this->getAValidProduct();
        ($this->handler)(
            new AddProductToCartCommand(
                cartId: $cart->id()->value(),
                productId: $product->id(),
                quantity: 2
            )
        );
        $firstCartProductFound = $this->getTheFirstProductFromTheCart($cart);
        $this->assertEquals(expected: 99.95, actual: $firstCartProductFound->unitPrice());
        $this->assertEquals(expected: 199.90, actual: $firstCartProductFound->totalPrice());
        $this->assertEquals(expected: 21, actual: $firstCartProductFound->taxRate());
        $this->assertEquals(expected: 20.99, actual: $firstCartProductFound->unitRate());
        $this->assertEquals(expected: 120.94, actual: $firstCartProductFound->unitAmount());
        $this->assertEquals(expected: 41.98, actual: $firstCartProductFound->totalRate());
        $this->assertEquals(expected: 241.88, actual: $firstCartProductFound->totalAmount());
    }

    protected function setUp(): void
    {
        $this->cartRepository = new InMemoryActiveCartRepository([
            self::EXISTING_CART_ID => CartMother::create(
                id: new CartId(self::EXISTING_CART_ID)
            ),
        ]);
        $this->productRepository = new InMemoryProductRepository([
            self::EXISTING_PRODUCT_ID => ProductMother::create(
                id: self::EXISTING_PRODUCT_ID,
                price: 99.95,
                taxRate: 21
            ),
        ]);
        $this->handler = new AddProductToCartCommandHandler(
            activeCartRepository: $this->cartRepository,
            productRepository: $this->productRepository
        );
    }
}
