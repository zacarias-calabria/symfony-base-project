<?php

declare(strict_types=1);

namespace Tests\App\Authorization\SignIn\Infrastructure\SignIn\Providers\Google;

use App\Authorization\SignIn\Infrastructure\SignIn\Providers\Google\HTTPSignInResponse;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

final class HTTPSignInResponseTest extends TestCase
{
    #[Test]
    #[Group('authorization')]
    #[Group('unit')]
    public function the_response_is_a_suitable_http_response(): void {
        $sut = new HTTPSignInResponse(
            null,
            Response::HTTP_OK,
            headers: ['Location' => 'http://localhost/redirection']
        );
        $response = $sut->response();
        $this->assertEmpty($response->getContent());
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('http://localhost/redirection', $response->headers->get('Location'));
    }

}
