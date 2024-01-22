<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/apps')
    ->in(__DIR__ . '/bin')
    ->in(__DIR__ . '/features')
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        '@PHP83Migration' => true,
        '@Symfony' => true,
    ])
    ->setFinder($finder);
