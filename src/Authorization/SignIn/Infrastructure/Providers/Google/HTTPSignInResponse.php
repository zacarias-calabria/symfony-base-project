<?php

declare(strict_types=1);

namespace App\Authorization\SignIn\Infrastructure\Providers\Google;

use App\Authorization\SignIn\Domain\SignInResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class HTTPSignInResponse implements SignInResponse
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        private ?string $content,
        private int $statusCode = Response::HTTP_OK,
        private array $headers = [],
    ) {
    }

    public function response(): Response
    {
        return new Response(
            content: $this->content,
            status: $this->statusCode,
            headers: $this->headers,
        );
    }
}
