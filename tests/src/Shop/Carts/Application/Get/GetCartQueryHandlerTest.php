<?php

declare(strict_types=1);

namespace Tests\App\Shop\Carts\Application\Get;

use App\Shared\Domain\Bus\Query\QueryHandler;
use App\Shop\Carts\Application\Get\GetCartQuery;
use App\Shop\Carts\Application\Get\GetCartQueryHandler;
use App\Shop\Carts\Domain\CartId;
use App\Shop\Carts\Domain\CartNotFound;
use App\Shop\Carts\Domain\CartRepository;
use App\Shop\Carts\Infrastructure\Persistence\InMemoryActiveCartRepository;
use App\Shop\Carts\Infrastructure\Persistence\InMemoryAllCartRepository;
use PHPUnit\Framework\TestCase;
use Tests\App\Shop\Carts\Domain\CartMother;

/**
 * @test
 * @group get_cart_command_handler
 * @group cart
 * @group unit
 */
class GetCartQueryHandlerTest extends TestCase
{
    private GetCartQueryHandler $handler;
    private CartRepository $repository;

    /**
     * @test
     * Be A Proper Class
     * be_a_proper_class
     */
    public function itShouldBeAProperClass(): void
    {
        $this->assertInstanceOf(GetCartQueryHandler::class, $this->handler);
        $this->assertInstanceOf(QueryHandler::class, $this->handler);
    }

    /**
     * @test
     * Throw A Cart Not Found Error If The Cart Doesnt Exists
     * throw_a_cart_not_found_error_if_the_cart_doesnt_exists
     */
    public function itShouldThrowACartNotFoundErrorIfTheCartDoesntExists(): void
    {
        $this->expectException(CartNotFound::class);
        $handler = new GetCartQueryHandler(
            cartRepository: new InMemoryActiveCartRepository([])
        );
        ($handler)(
            query: new GetCartQuery(
                id: (CartId::random())->value()
            )
        );
    }

    /**
     * @test
     * Find An Existing Cart
     * find_an_existing_cart
     */
    public function itShouldFindAnExistingCart(): void
    {
        $cart = CartMother::create();
        $handler = new GetCartQueryHandler(
            cartRepository: new InMemoryActiveCartRepository([$cart->id()->value() => $cart])
        );
        $cartFound = ($handler)(
            query: new GetCartQuery(
                id: $cart->id()->value()
            )
        );
        $this->assertEquals(
            expected: $cart->id()->value(),
            actual: $cartFound->id()
        );
        $this->assertEquals(
            expected: $cart->status(),
            actual: $cartFound->status()
        );
        $this->assertEquals(
            expected: $cart->createdAt()->format('Y-m-d H:i:s'),
            actual: $cartFound->createdAt()
        );
    }

    protected function setUp(): void
    {
        $this->repository = new InMemoryAllCartRepository([]);
        $this->handler = new GetCartQueryHandler(
            cartRepository: $this->repository
        );
    }
}
