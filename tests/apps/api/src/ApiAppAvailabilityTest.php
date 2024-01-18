<?php

declare(strict_types=1);

namespace Tests\Apps\API;

use Generator;
use Tests\App\Shared\Infrastructure\PhpUnit\ApiAppWebTestCase;


class ApiAppAvailabilityTest extends ApiAppWebTestCase
{
    /**
     * @test
     * Request Check Routes Successfully
     * request_check_routes_successfully
     * @group api_app_availability
     * @group api_app
     * @group functionality
     *
     * @dataProvider checkRoutesProvider
     */
    public function itShouldRequestCheckRoutesSuccessfully(string $method, string $route): void
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
