<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Framework\Symfony;

use App\Shared\Domain\Bus\Command\Command;
use App\Shared\Domain\Bus\Query\Query;
use App\Shared\Domain\Bus\Query\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

use function Lambdish\Phunctional\each;

abstract class ApiController
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly MessageBusInterface $queryBus,
        ApiExceptionsHttpStatusCodeMapping $exceptionHandler,
    ) {
        each(
            fn(int $httpCode, string $exceptionClass) => $exceptionHandler->register($exceptionClass, $httpCode), // @phpstan-ignore-line
            $this->exceptions(),
        );
    }

    /**
     * @return array<class-string,int>
     */
    abstract protected function exceptions(): array;

    protected function ask(Query $query): ?Response
    {
        /** @var HandledStamp $stamp */
        $stamp = $this->queryBus->dispatch($query)->last(HandledStamp::class);
        return $stamp->getResult();
    }

    public function dispatch(Command $command): void
    {
        $this->commandBus->dispatch($command);
    }
}
