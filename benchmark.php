<?php

use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use Hyperized\Benchmark\Benchmark;

require __DIR__ . DIRECTORY_SEPARATOR . 'autoload.php';

$builder = new ContainerBuilder();
$container = $builder->build();

echo '<pre>';
try {
    $benchmark = $container->get(Benchmark::class);
} catch (DependencyException $e) {
    \print_r($e);
} catch (NotFoundException $e) {
    \print_r($e);
}
echo '</pre>';

