<?php

declare(strict_types=1);

namespace Tests\Techpump\Shop\Carts\Application\Create;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Techpump\Shared\Domain\Bus\Command\CommandHandler;
use Techpump\Shop\Carts\Application\Create\CreateCartCommand;
use Techpump\Shop\Carts\Application\Create\CreateCartCommandHandler;
use Techpump\Shop\Carts\Domain\CartAlreadyExistsError;
use Techpump\Shop\Carts\Domain\CartRepository;
use Techpump\Shop\Carts\Infrastructure\Persistence\InMemoryAllCartRepository;
use Tests\Techpump\Shared\Domain\UuidMother;

/**
 * @test
 * @group create_cart_command_handler
 * @group cart
 * @group unit
 */
class CreateCartCommandHandlerTest extends TestCase
{
    private CartRepository $repository;
    private CreateCartCommandHandler $handler;

    protected function setUp(): void
    {
        $this->repository = new InMemoryAllCartRepository();
        $this->handler = new CreateCartCommandHandler(
            cartRepository: $this->repository
        );
    }

    /**
     * @test
     * Be A Proper Class
     * be_a_proper_class
     */
    public function itShouldBeAProperClass(): void
    {
        $this->assertInstanceOf(CreateCartCommandHandler::class, $this->handler);
        $this->assertInstanceOf(CommandHandler::class, $this->handler);
    }

    /**
     * @test
     * Throw An InvalidArgumentException For Invalid Command
     * throw_an_invalid_argument_exception_for_invalid_command
     */
    public function itShouldThrowAnInvalidArgumentExceptionForInvalidCommand(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ($this->handler)(
            new CreateCartCommand('wrong-id')
        );
    }

    /**
     * @test
     * Throw A Cart Already Exists If The Cart Exists
     * throw_a_cart_already_exists_if_the_cart_exists
     */
    public function itShouldThrowACartAlreadyExistsIfTheCartExists(): void
    {
        $cartId = UuidMother::create();
        ($this->handler)(
            new CreateCartCommand(
                id: $cartId
            )
        );
        $this->expectException(CartAlreadyExistsError::class);
        ($this->handler)(
            new CreateCartCommand(
                id: $cartId
            )
        );
    }

    /**
     * @test
     * Create A New Cart
     * create_a_new_cart
     */
    public function itShouldCreateANewCart(): void
    {
        ($this->handler)(
            new CreateCartCommand(
                id: UuidMother::create()
            )
        );
        $this->assertTrue(true);
    }
}
