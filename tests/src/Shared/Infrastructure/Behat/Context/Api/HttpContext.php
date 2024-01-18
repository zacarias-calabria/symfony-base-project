<?php

declare(strict_types=1);

namespace Tests\App\Shared\Infrastructure\Behat\Context\Api;

use Behat\Gherkin\Node\PyStringNode;
use Behat\MinkExtension\Context\RawMinkContext;
use RuntimeException;
use Tests\App\Shared\Infrastructure\Behat\Client\ApiClient;

final class HttpContext extends RawMinkContext
{
    private ?string $payload = null;

    public function __construct(
        private readonly ApiClient $apiClient
    ) {
    }

    /**
     * @Given /^I have the payload$/i
     */
    public function iHaveThePayload(PyStringNode $payload): void
    {
        $this->payload = $payload->getRaw();
    }

    /**
     * @When /^I send a (GET|POST|PUT|PATCH|DELETE) request to (\S+)$/i
     */
    public function iSendARequestTo(string $method, string $uri): void
    {
        $this->apiClient->request(
            $method,
            $uri,
            ['content' => $this->payload]
        );
    }

    /**
     * @When /^I send a (GET|POST|PUT|PATCH|DELETE) request to (\S+) with body\:$/i
     */
    public function iSendARequestToWithBody(string $method, string $uri, PyStringNode $body): void
    {
        $this->apiClient->request(
            $method,
            $uri,
            ['content' => $body->getRaw()]
        );
    }

    /**
     * @Then /^the response status code should be (\d+)$/i
     */
    public function theResponseStatusCodeShouldBe(int $expectedResponseCode): void
    {
        if ($this->getSession()->getStatusCode() !== $expectedResponseCode) {
            throw new RuntimeException(
                sprintf(
                    'The status code <%1$d> does not match the expected <%2$d>' . PHP_EOL . 'Message: %3$s',
                    $this->getSession()->getStatusCode(),
                    $expectedResponseCode,
                    $this->getSession()->getPage()->getContent()
                )
            );
        }
    }
}
