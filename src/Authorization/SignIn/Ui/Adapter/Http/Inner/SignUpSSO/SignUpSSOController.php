<?php

declare(strict_types=1);

namespace App\Authorization\SignIn\Ui\Adapter\Http\Inner\SignUpSSO;

use App\Authorization\SignIn\Application\SignUpSSO\OAuthURL;
use App\Authorization\SignIn\Application\SignUpSSO\OAuthURLResponse;
use App\Shared\Infrastructure\Framework\Symfony\ApiController;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/signup/google', methods: ['GET'])]
final class SignUpSSOController extends ApiController
{
    /**
     * @throws ExceptionInterface
     */
    public function __invoke(): JsonResponse
    {
        /** @var OAuthURLResponse $response */
        $response = $this->ask(new OAuthURL());
        return new JsonResponse(
            [],
            Response::HTTP_FOUND,
            [
                'Location' => $response->location,
            ],
        );
    }

    #[\Override]
    protected function exceptions(): array
    {
        return [];
    }
}
