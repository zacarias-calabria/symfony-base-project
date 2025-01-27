<?php

use App\Apps\Head\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__.'/../../vendor/autoload.php';

new Dotenv()->bootEnv(__DIR__ . '/../../apps/shared/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
return $kernel->getContainer()->get('doctrine')->getManager();
