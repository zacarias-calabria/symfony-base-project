<?php

namespace Tests\App\Authorization\SignIn\Infrastructure\Providers\Google;

use App\Authorization\SignIn\Infrastructure\Providers\Google\GoogleAuthorizer;
use Exception;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\App\Shared\Infrastructure\PhpUnit\HeadInfrastructureTestCase;

class GoogleAuthorizerTest extends HeadInfrastructureTestCase
{
    /**
     * @throws Exception
     */
    #[Test]
    #[Group('authorization')]
    #[Group('unit')]
    public function should_generate_a_response_with_an_authorization_url_header_location(): void
    {
        $providerClient = new GoogleClientDouble();
        $sut = new GoogleAuthorizer(providerClient: $providerClient);
        $response = $sut->signIn();
        /** @var Response $httpResponse */
        $httpResponse = $response->response();
        $headers = $httpResponse->headers->allPreserveCaseWithoutCookies();
        self::assertIsArray($headers);
        self::assertArrayHasKey('Location', $headers);
        self::assertEquals($providerClient->createAuthUrl(), $headers['Location'][0]);
    }
}
