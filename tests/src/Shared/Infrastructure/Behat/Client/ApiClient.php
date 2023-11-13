<?php

declare(strict_types=1);

namespace Tests\Techpump\Shared\Infrastructure\Behat\Client;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Session;
use Symfony\Component\BrowserKit\AbstractBrowser;

class ApiClient
{
    public function __construct(private readonly Session $session)
    {
    }

    public function request(
        string $method,
        string $uri,
        array $optionalParams = []
    ): void {
        $defaultOptionalParams = [
            'parameters' => [],
            'files' => [],
            'server' => [],
            'content' => null,
            'changeHistory' => true,
        ];

        $optionalParams = array_merge($defaultOptionalParams, $optionalParams);

        $this->getClient()->request(
            $method,
            $uri,
            $optionalParams['parameters'],
            $optionalParams['files'],
            $optionalParams['server'],
            $optionalParams['content'],
            $optionalParams['changeHistory']
        );
    }

    private function getClient(): AbstractBrowser
    {
        return $this->getDriver()->getClient();
    }

    private function getDriver(): DriverInterface
    {
        return $this->getSession()->getDriver();
    }

    private function getSession(): Session
    {
        return $this->session;
    }
}
