<?php

declare(strict_types=1);

namespace Tests\App\Shop\Carts\Infrastructure\Persistence;

use App\Shop\Carts\Domain\CartId;
use App\Shop\Carts\Domain\CartNotFound;
use App\Shop\Carts\Domain\CartRepository;
use App\Shop\Carts\Domain\CartRepositoryError;
use App\Shop\Carts\Infrastructure\Persistence\InMemoryActiveCartRepository;
use PHPUnit\Framework\TestCase;
use Tests\App\Shop\Carts\Domain\CartIdMother;
use Tests\App\Shop\Carts\Domain\CartMother;

/**
 * @test
 * @group in_memory_cart_repository
 * @group cart
 * @group unit
 */
class InMemoryActiveCartRepositoryTest extends TestCase
{
    /**
     * @test
     * Be a proper class
     * be_a_proper_class
     */
    public function itShouldBeAProperClass(): void
    {
        $repository = new InMemoryActiveCartRepository();

        $this->assertInstanceOf(InMemoryActiveCartRepository::class, $repository);
        $this->assertInstanceOf(CartRepository::class, $repository);
    }

    /**
     * @test
     * Throw a CartRepositoryException if save fails
     * throw_a_cart_repository_exception_if_save_fail
     */
    public function itShouldThrowACartRepositoryExceptionIfSaveFails(): void
    {
        $this->expectException(CartRepositoryError::class);

        $repository = new InMemoryActiveCartRepository();
        $repository->throwAnExceptionOnNextMethodCall();

        $repository->save(CartMother::create());
    }

    /**
     * @test
     * Save A Cart
     * save_a_cart
     */
    public function itShouldSaveACart(): void
    {
        $repository = new InMemoryActiveCartRepository();

        $repository->save(CartMother::create());

        $this->assertTrue(true);
    }

    /**
     * @test
     * Throw A Cart Not Found Exception If Cart Does Not Exists
     * throw_a_cart_not_found_exception_if_cart_does_not_exists
     * @group in_memory_cart_repository
     */
    public function itShouldThrowACartNotFoundExceptionIfCartDoesNotExists(): void
    {
        $this->expectException(CartNotFound::class);
        $repository = new InMemoryActiveCartRepository();
        $repository->search(
            id: CartIdMother::create()
        );
    }

    /**
     * @test
     * Find A Cart
     * find_a_cart
     */
    public function itShouldFindACart(): void
    {
        $repository = new InMemoryActiveCartRepository();
        $cart = CartMother::create();
        $repository->save($cart);
        $foundCart = $repository->search(
            id: new CartId(
                $cart->id()->value()
            )
        );
        $this->assertEquals(
            expected: $cart->id()->value(),
            actual: $foundCart->id()->value()
        );
        $this->assertEquals(
            expected: $cart->status(),
            actual: $foundCart->status()
        );
        $this->assertEquals(
            expected: $cart->createdAt(),
            actual: $foundCart->createdAt()
        );
    }
}
