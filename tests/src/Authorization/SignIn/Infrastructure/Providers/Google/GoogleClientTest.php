<?php

declare(strict_types=1);

namespace Tests\App\Authorization\SignIn\Infrastructure\Providers\Google;

use JsonException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\App\Shared\Infrastructure\PhpUnit\ApiAppWebTestCase;

class GoogleClientTest extends ApiAppWebTestCase {
    /**
     * @throws JsonException
     */
    #[Test]
    #[Group('authorization')]
    #[Group('integration')]
    public function should_create_an_authorization_url(): void {
        $configFile = __DIR__.'/../../../../../../../resources/GoogleCloud/client_secret_1063523097967-f03a92mtt9ff81j1efobgkpp3dckhbrt.apps.googleusercontent.com.json';
        $jsonContent = file_get_contents($configFile);
        assert($jsonContent !== false);
        $config = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);
        $sut = new \App\Authorization\SignIn\Infrastructure\Providers\Google\GoogleClient($config);
        $sut->setRedirectUri('http://localhost');
        $sut->setAccessType('offline');
        $sut->setClientId('clientIdTest');
        $sut->setState('xyz');
        $authUrl = $sut->createAuthUrl('http://googleapis.com/scope/test');
        $expected = "https://accounts.google.com/o/oauth2/v2/auth"
            . "?response_type=code"
            . "&access_type=offline"
            . "&client_id=clientIdTest"
            . "&redirect_uri=http%3A%2F%2Flocalhost"
            . "&state=xyz"
            . "&scope=http%3A%2F%2Fgoogleapis.com%2Fscope%2Ftest"
            . "&approval_prompt=auto";
        $this->assertEquals($expected, $authUrl);
    }
}
