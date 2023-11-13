<?php

declare(strict_types=1);

namespace Tests\Techpump\Shop\Carts\Infrastructure\Persistence;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Techpump\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;
use Techpump\Shop\Carts\Domain\CartNotFound;
use Techpump\Shop\Carts\Domain\CartRepository;
use Techpump\Shop\Carts\Domain\CartRepositoryError;
use Techpump\Shop\Carts\Infrastructure\Persistence\DoctrineAllCartRepository;
use Tests\Techpump\Shared\Domain\UuidMother;
use Tests\Techpump\Shared\Infrastructure\PhpUnit\AppContextInfrastructureTestCase;
use Tests\Techpump\Shop\Carts\Domain\CartIdMother;
use Tests\Techpump\Shop\Carts\Domain\CartMother;

/**
 * @test
 * @group doctrine_car_repository
 * @group cart
 * @group integration
 */
class DoctrineAllCartRepositoryTest extends AppContextInfrastructureTestCase
{
    private DoctrineAllCartRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var Registry $service */
        $service = $this->service('doctrine');
        $entityManager = $service->getManager();
        $this->repository = new DoctrineAllCartRepository(entityManager: $entityManager);
    }

    /**
     * @test
     * Be a proper class
     * be_a_proper_class
     */
    public function itShouldBeAProperClass(): void
    {
        $this->assertInstanceOf(DoctrineAllCartRepository::class, $this->repository);
        $this->assertInstanceOf(CartRepository::class, $this->repository);
        $this->assertInstanceOf(DoctrineRepository::class, $this->repository);
    }

    /**
     * @test
     * Save A Cart
     * save_a_cart
     */
    public function itShouldSaveACart(): void
    {
        $this->repository->save(CartMother::create());
        $this->assertTrue(true);
    }

    /**
     * @test
     * Throw An Exception If Cart Id Already Exists
     * throw_an_exception_if_cart_id_already_exists
     */
    public function itShouldThrowAnExceptionIfCartIdAlreadyExists(): void
    {
        $this->expectException(CartRepositoryError::class);
        $cartId = CartIdMother::create(
            value: UuidMother::create()
        );
        $this->repository->save(CartMother::create($cartId));
        $this->repository->save(CartMother::create($cartId));
    }

    /**
     * @test
     * Throw A Cart Not Found Exception If The Doesnt Exist
     * throw_a_cart_not_found_exception_if_the_doesnt_exist
     */
    public function itShouldThrowACartNotFoundExceptionIfTheDoesntExist(): void
    {
        $this->expectException(CartNotFound::class);
        $this->repository->search(CartIdMother::create());
    }

    /**
     * @test
     * Return A Existing Cart
     * return_a_existing_cart
     */
    public function itShouldReturnAExistingCart(): void
    {
        $cart = CartMother::create();
        $this->repository->save($cart);
        $foundCart = $this->repository->search(
            id: $cart->id()
        );
        $this->assertEquals(
            expected: $cart,
            actual: $foundCart
        );
    }
}
