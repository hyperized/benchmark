<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Modules;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Result\MemoryResult;

class Memory implements BenchmarkModuleInterface
{
    private static int $defaultCount = 9999;

    public function __construct(private readonly Config $config) {}

    public function isEnabled(): bool
    {
        return (bool) $this->config->get('benchmark.memory.enabled');
    }

    public function run(): MemoryResult
    {
        $count = (int) ($this->config->get('benchmark.memory.count') ?? self::$defaultCount);

        /** @var array<string, float> $times */
        $times = [];
        /** @var array<string, int> $peaks */
        $peaks = [];

        $this->benchmark('Array Fill', $count, $times, $peaks, function (int $count): void {
            $arr = [];
            for ($i = 0; $i < $count; $i++) {
                $arr[] = $i;
            }
        });

        $this->benchmark('Array Sort', $count, $times, $peaks, function (int $count): void {
            $arr = \range($count, 0, -1);
            \sort($arr);
        });

        $this->benchmark('String Concat', $count, $times, $peaks, function (int $count): void {
            $str = '';
            for ($i = 0; $i < $count; $i++) {
                $str .= 'x';
            }
        });

        $this->benchmark('Object Creation', $count, $times, $peaks, function (int $count): void {
            $objects = [];
            for ($i = 0; $i < $count; $i++) {
                $objects[] = new \stdClass();
            }
        });

        $this->benchmark('Array Map', $count, $times, $peaks, function (int $count): void {
            $arr = \range(0, $count - 1);
            \array_map(static fn(int $v): int => $v * 2, $arr);
        });

        $this->benchmark('Array Merge', $count, $times, $peaks, function (int $count): void {
            $a = \range(0, (int) ($count / 2) - 1);
            $b = \range((int) ($count / 2), $count - 1);
            $_ = \array_merge($a, $b);
        });

        return new MemoryResult(
            allocationTimes: $times,
            peakMemoryUsages: $peaks,
            cycles: $count,
        );
    }

    /**
     * @param array<string, float> $times
     * @param array<string, int> $peaks
     * @param callable(int): void $fn
     */
    private function benchmark(string $name, int $count, array &$times, array &$peaks, callable $fn): void
    {
        \gc_collect_cycles();
        $memBefore = \memory_get_usage(true);

        $start = \microtime(true);
        $fn($count);
        $times[$name] = \microtime(true) - $start;

        $peaks[$name] = \memory_get_usage(true) - $memBefore;
    }
}
