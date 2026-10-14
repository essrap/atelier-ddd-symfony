<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude(['var', 'vendor'])
    ->notPath('config/reference.php')
    ->append([__DIR__.'/bin/console', __DIR__.'/bin/phpunit']);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@PER-CS' => true,
        'declare_strict_types' => true,
        'final_class' => true,
    ])
    ->setFinder($finder);
