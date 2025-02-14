<?php

use Symfony\Component\Dotenv\Dotenv;

$rootPath = dirname(__DIR__);
$environmentFilesPath = dirname(__DIR__).'/apps/shared';

require $rootPath . '/vendor/autoload.php';

// Load cached env vars if the .env.local.php file exists
// Run "composer dump-env prod" to create it (requires symfony/flex >=1.2)
if (is_array($env = @include $environmentFilesPath . '/.env.local.php')) {
    foreach ($env as $k => $v) {
        $_ENV[$k] ??= isset($_SERVER[$k]) && str_starts_with($k, 'HTTP_') ? $_SERVER[$k] : $v;
    }
} elseif (!class_exists(Dotenv::class)) {
    throw new RuntimeException(
        'Please run "composer require symfony/dotenv" to load the ".env" files configuring the application.'
    );
} else {
    // load all the .env files
    new Dotenv(false)->loadEnv($environmentFilesPath . '/.env');
}

$_SERVER += $_ENV;
$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null) ?: 'dev';
$_SERVER['APP_DEBUG'] ??= $_ENV['APP_DEBUG'] ?? 'prod' !== $_SERVER['APP_ENV'];
$_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = (int)$_SERVER['APP_DEBUG'] || filter_var(
    $_SERVER['APP_DEBUG'],
    FILTER_VALIDATE_BOOLEAN
) ? '1' : '0';
