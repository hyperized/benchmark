<?php

declare(strict_types=1);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS2.0' => true,
        'strict_param' => true,
        'declare_strict_types' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()->in(__DIR__ . '/src')
    );
