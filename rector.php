<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/apps',
        __DIR__ . '/bin',
        __DIR__ . '/features',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    // define sets of rules
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_83,
        SymfonySetList::SYMFONY_64,
        SymfonySetList::SYMFONY_CODE_QUALITY
    ]);
    };
