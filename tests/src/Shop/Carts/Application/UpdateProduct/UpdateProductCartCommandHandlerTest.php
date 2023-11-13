<?php

declare(strict_types=1);

namespace Tests\Techpump\Shop\Carts\Application\UpdateProduct;

use PHPUnit\Framework\TestCase;
use Techpump\Inventory\Products\Domain\Product;
use Techpump\Shared\Domain\Bus\Command\CommandHandler;
use Techpump\Shop\Carts\Application\AddProduct\AddProductToCartCommand;
use Techpump\Shop\Carts\Application\UpdateProduct\UpdateProductCartCommand;
use Techpump\Shop\Carts\Application\UpdateProduct\UpdateProductCartCommandHandler;
use Techpump\Shop\Carts\Domain\Cart;
use Techpump\Shop\Carts\Domain\CartId;
use Techpump\Shop\Carts\Domain\CartNotFound;
use Techpump\Shop\Carts\Domain\InsufficientQuantityProductsError;
use Techpump\Shop\Carts\Domain\ProductInCart;
use Techpump\Shop\Carts\Domain\ProductInCartNotFound;
use Techpump\Shop\Carts\Infrastructure\Persistence\InMemoryActiveCartRepository;
use Tests\Techpump\Inventory\Products\Domain\ProductMother;
use Tests\Techpump\Shop\Carts\Domain\CartMother;

use function Lambdish\Phunctional\first;

/**
 * @test
 * @group update_product_cart_command_handler
 * @group unit
 */
class UpdateProductCartCommandHandlerTest extends TestCase
{
    private const EXISTING_CART_ID = 'cddf970c-d7b8-4d2b-9fcd-3f9644de27d6';
    private const EXISTING_PRODUCT_ID = '7e912f83-b3cd-42c6-ab05-579a841b8b1c';
    private const EXISTING_NOT_IN_CART_PRODUCT_ID = 'def54ed5-dce7-4985-991d-1de1824ae625';
    private UpdateProductCartCommandHandler $handler;
    private InMemoryActiveCartRepository $cartRepository;

    protected function setUp(): void
    {
        $this->cartRepository = new InMemoryActiveCartRepository([
            self::EXISTING_CART_ID => CartMother::create(
                id: new CartId(self::EXISTING_CART_ID)
            ),
        ]);
        $this->handler = new UpdateProductCartCommandHandler(
            activeCartRepository: $this->cartRepository,
        );
    }

    /**
     * @test
     * Be A Proper Class
     * be_a_proper_class
     */
    public function itShouldBeAProperClass(): void
    {
        $this->assertInstanceOf(UpdateProductCartCommandHandler::class, $this->handler);
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
            new UpdateProductCartCommand(
                cartId: '6e35c1a5-4b5c-41ce-bffe-78efb5bd0823',
                productId: 'ec92351f-8b69-4120-8cb0-8dcb080a4e73',
                quantity: 4
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
            new UpdateProductCartCommand(
                cartId: self::EXISTING_CART_ID,
                productId: self::EXISTING_NOT_IN_CART_PRODUCT_ID,
                quantity: 4
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
        $cart = $this->getAValidCart();
        $product = $this->getAValidProduct();
        $this->addAProductToCart($cart, $product);
        ($this->handler)(
            new UpdateProductCartCommand(
                cartId: $cart->id()->value(),
                productId: $product->id(),
                quantity: 0
            )
        );
    }

    /**
     * @test
     * Update An Existing Product Cart
     * update_an_existing_product_cart
     */
    public function itShouldUpdateAnExistingProductCart(): void
    {
        $cart = $this->getAValidCart();
        $product = $this->getAValidProduct();
        $this->addAProductToCart($cart, $product);
        ($this->handler)(
            new UpdateProductCartCommand(
                cartId: $cart->id()->value(),
                productId: $product->id(),
                quantity: 4
            )
        );
        $this->assertCount(
            expectedCount: 1,
            haystack: $cart->productsInCart()
        );
        /** @var ProductInCart $firstCartProductFound */
        $firstCartProductFound = first($cart->productsInCart());
        $this->assertEquals(
            expected: [
                $cart,
                $product,
                $product->price(),
                $product->taxRate(),
                4,
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
        return ProductMother::create(self::EXISTING_PRODUCT_ID);
    }

    private function addAProductToCart(Cart $cart, Product $product): void
    {
        $cart->addProductToCart(
            productInCart: ProductInCart::create(
                cart: $cart,
                product: $product,
                unitPrice: $product->price(),
                taxRate: $product->taxRate(),
                quantity: 2
            )
        );
    }
}
