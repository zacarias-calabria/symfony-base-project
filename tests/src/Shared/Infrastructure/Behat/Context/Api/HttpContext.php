<?php

declare(strict_types=1);

namespace Tests\App\Shared\Infrastructure\Behat\Context\Api;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Hook\BeforeScenario;
use Behat\MinkExtension\Context\RawMinkContext;
use Exception;
use PHPUnit\Framework\Assert;
use Tests\App\Shared\Infrastructure\Behat\Client\ApiClient;

final class HttpContext extends RawMinkContext
{
    private ?string $payload = null;

    public function __construct(
        private readonly ApiClient $apiClient,
    ) {}

    #[BeforeScenario]
    public function interceptRedirections(BeforeScenarioScope $context): void
    {
        $tags = $context->getScenario()->getTags();
        if (in_array('not-follow-redirects', $tags, true)) {
            $this->apiClient->followRedirects(followRedirects: false);
        }
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
        $this->apiClient->followRedirects(followRedirects: false);
        $this->apiClient->request(
            $method,
            $uri,
            ['content' => $this->payload],
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
            ['content' => $body->getRaw()],
        );
    }

    /**
     * @Then /^the response status code should be (\d+)$/i
     */
    public function theResponseStatusCodeShouldBe(int $expectedResponseCode): void
    {
        if ($this->getSession()->getStatusCode() !== $expectedResponseCode) {
            throw new \RuntimeException(
                sprintf(
                    'The status code <%1$d> does not match the expected <%2$d>' . PHP_EOL . 'Message: %3$s',
                    $this->getSession()->getStatusCode(),
                    $expectedResponseCode,
                    $this->getSession()->getPage()->getContent(),
                ),
            );
        }
    }

    /**
     * @Given /^the response should the header (\S+) with the payload$/i
     * @throws Exception
     */
    public function theResponseShouldTheHeaderLocationWithThePayload(string $header, PyStringNode $payload): void
    {
        $responseHeader = $this->getSession()->getResponseHeader($header);
        if ($responseHeader === null) {
            throw new Exception(sprintf('The header %1$s is not present in the response', $header));
        }
        if ($responseHeader !== $payload->getRaw()) {
            throw new Exception(sprintf('The header %1$s payload not has expected\nwe have received %2$s\nand expected %3$s', $header, $responseHeader, $payload->getRaw()));

        }
    }
}
