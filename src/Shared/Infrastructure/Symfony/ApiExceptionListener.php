<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony;

use App\Shared\Domain\DomainError;
use App\Shared\Domain\Utils\Classes;
use App\Shared\Domain\Utils\Strings;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Throwable;

final readonly class ApiExceptionListener
{
    public function __construct(private ApiExceptionsHttpStatusCodeMapping $exceptionHandler)
    {
    }

    public function onException(ExceptionEvent $event): void
    {
        $exception = $this->extractSuitableException(event: $event);
        $event->setResponse(
            new JsonResponse(
                [
                    'code' => $this->exceptionCodeFor($exception),
                    'message' => $exception->getMessage(),
                ],
                $this->exceptionHandler->statusCodeFor($exception::class)
            )
        );
    }

    private function extractSuitableException(ExceptionEvent $event): Throwable
    {
        $throwable = $event->getThrowable();
        return $throwable->getPrevious() ?? $throwable;
    }

    private function exceptionCodeFor(Throwable $error): string
    {
        $domainErrorClass = DomainError::class;

        return $error instanceof $domainErrorClass
            ? $error->errorCode()
            : Strings::toSnakeCase(Classes::extractClassName($error));
    }
}
