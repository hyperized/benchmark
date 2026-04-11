<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Modules;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Result\FunctionOverheadResult;

class FunctionOverhead implements BenchmarkModuleInterface
{
    private static int $defaultCount = 999999;

    public function __construct(private readonly Config $config) {}

    public function isEnabled(): bool
    {
        return (bool) $this->config->get('benchmark.functionOverhead.enabled');
    }

    public function run(): FunctionOverheadResult
    {
        $count = (int) ($this->config->get('benchmark.functionOverhead.count') ?? self::$defaultCount);

        /** @var array<string, float> $times */
        $times = [];

        // Named function
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $_ = \abs($i);
        }
        $times['Named Function'] = \microtime(true) - $start;

        // Closure
        $closure = static function (int $v): int {
            return $v >= 0 ? $v : -$v;
        };
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $_ = $closure($i);
        }
        $times['Closure'] = \microtime(true) - $start;

        // First-class callable
        $callable = \abs(...);
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $_ = $callable($i);
        }
        $times['First-class Callable'] = \microtime(true) - $start;

        // Static method
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $_ = self::staticMethod($i);
        }
        $times['Static Method'] = \microtime(true) - $start;

        // Instance method
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $_ = $this->instanceMethod($i);
        }
        $times['Instance Method'] = \microtime(true) - $start;

        // Arrow function
        $arrow = static fn(int $v): int => $v >= 0 ? $v : -$v;
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $_ = $arrow($i);
        }
        $times['Arrow Function'] = \microtime(true) - $start;

        return new FunctionOverheadResult(
            callTimes: $times,
            cycles: $count,
        );
    }

    private static function staticMethod(int $v): int
    {
        return $v >= 0 ? $v : -$v;
    }

    private function instanceMethod(int $v): int
    {
        return $v >= 0 ? $v : -$v;
    }
}
