<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Modules;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Result\AutoloadingResult;

class Autoloading implements BenchmarkModuleInterface
{
    private static int $defaultCount = 99999;

    public function __construct(private readonly Config $config) {}

    public function isEnabled(): bool
    {
        return (bool) $this->config->get('benchmark.autoloading.enabled');
    }

    public function run(): AutoloadingResult
    {
        $count = (int) ($this->config->get('benchmark.autoloading.count') ?? self::$defaultCount);

        /** @var array<string, float> $times */
        $times = [];

        // stdClass
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $_ = new \stdClass();
        }
        $times['stdClass'] = \microtime(true) - $start;

        // DateTime
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            new \DateTime();
        }
        $times['DateTime'] = \microtime(true) - $start;

        // DateTimeImmutable
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            new \DateTimeImmutable();
        }
        $times['DateTimeImmutable'] = \microtime(true) - $start;

        // SplStack
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $_ = new \SplStack();
        }
        $times['SplStack'] = \microtime(true) - $start;

        // SplPriorityQueue
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $_ = new \SplPriorityQueue();
        }
        $times['SplPriorityQueue'] = \microtime(true) - $start;

        // ArrayObject
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            new \ArrayObject();
        }
        $times['ArrayObject'] = \microtime(true) - $start;

        return new AutoloadingResult(
            instantiationTimes: $times,
            cycles: $count,
        );
    }
}
