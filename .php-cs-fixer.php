<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/apps')
    ->in(__DIR__ . '/bin')
    ->in(__DIR__ . '/features')
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

return (new PhpCsFixer\Config())
    ->setRules([
//        '@PHP83Migration' => true,
        '@PSR12' => true,
//        '@Symfony' => true,
    ])
    ->setFinder($finder);
