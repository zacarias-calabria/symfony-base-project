<?php

declare(strict_types=1);

namespace Tests\App\Shop\Carts\Infrastructure\Persistence;

use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;
use App\Shop\Carts\Domain\CartNotFound;
use App\Shop\Carts\Domain\CartRepository;
use App\Shop\Carts\Domain\CartRepositoryError;
use App\Shop\Carts\Infrastructure\Persistence\DoctrineAllCartRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Tests\App\Shared\Domain\UuidMother;
use Tests\App\Shared\Infrastructure\PhpUnit\AppContextInfrastructureTestCase;
use Tests\App\Shop\Carts\Domain\CartIdMother;
use Tests\App\Shop\Carts\Domain\CartMother;

/**
 * @test
 * @group doctrine_car_repository
 * @group cart
 * @group integration
 */
class DoctrineAllCartRepositoryTest extends AppContextInfrastructureTestCase
{
    private DoctrineAllCartRepository $repository;

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

    protected function setUp(): void
    {
        parent::setUp();
        /** @var Registry $service */
        $service = $this->service('doctrine');
        $entityManager = $service->getManager();
        $this->repository = new DoctrineAllCartRepository(entityManager: $entityManager);
    }
}
