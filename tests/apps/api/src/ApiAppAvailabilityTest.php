<?php

declare(strict_types=1);

namespace Tests\Apps\API;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\App\Shared\Infrastructure\PhpUnit\ApiAppWebTestCase;

class ApiAppAvailabilityTest extends ApiAppWebTestCase
{
    #[Test]
    #[Group('$name')]
    #[Group('api_app_availability')]
    #[Group('api_app')]
    #[Group('functionality')]
    #[DataProvider('checkRoutesProvider')]
    public function request_check_routes_successfully(string $method, string $route): void
    {
        $client = self::createClient();
        $client->request(method: $method, uri: $route);
        self::assertResponseIsSuccessful();
    }

    public static function checkRoutesProvider(): Generator
    {
        yield ['GET', '/health-check'];
    }
}
