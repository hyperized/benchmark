<?php

require(__DIR__ . DIRECTORY_SEPARATOR . 'autoload.php');

$builder = new \DI\ContainerBuilder();
$container = $builder->build();

echo '<pre>';
try {
    $benchmark = $container->get('\Hyperized\Benchmark\Benchmark');
} catch (\DI\DependencyException $e) {
    print_r($e);
} catch (\DI\NotFoundException $e) {
    print_r($e);
}
echo '</pre>';

