<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Hyperized\Benchmark\Benchmark;
use Hyperized\Benchmark\Report\ReportWriter;

require __DIR__ . DIRECTORY_SEPARATOR . 'autoload.php';

$builder = new ContainerBuilder();
$builder->addDefinitions([
    ReportWriter::class => \DI\create(ReportWriter::class)
        ->constructor(__DIR__ . DIRECTORY_SEPARATOR . 'reports'),
]);
$container = $builder->build();

try {
    $benchmark = $container->get(Benchmark::class);
    $benchmark->execute();
} catch (\Throwable $e) {
    if (\php_sapi_name() === 'cli') {
        \fwrite(STDERR, $e->getMessage() . "\n");
        exit(1);
    }
    echo '<pre>' . \htmlspecialchars($e->getMessage(), ENT_QUOTES | ENT_HTML5) . '</pre>';
}
