<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Modules;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Result\TypeCastingResult;

class TypeCasting implements BenchmarkModuleInterface
{
    private static int $defaultCount = 999999;

    public function __construct(private readonly Config $config) {}

    public function isEnabled(): bool
    {
        return (bool) $this->config->get('benchmark.typeCasting.enabled');
    }

    public function run(): TypeCastingResult
    {
        $count = (int) ($this->config->get('benchmark.typeCasting.count') ?? self::$defaultCount);

        /** @var array<string, float> $times */
        $times = [];

        // int to string
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $_ = (string) $i;
        }
        $times['int → string'] = \microtime(true) - $start;

        // string to int
        $str = '12345';
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $_ = (int) $str;
        }
        $times['string → int'] = \microtime(true) - $start;

        // int to float
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $_ = (float) $i;
        }
        $times['int → float'] = \microtime(true) - $start;

        // float to int
        $float = 3.14159;
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $_ = (int) $float;
        }
        $times['float → int'] = \microtime(true) - $start;

        // string to float
        $floatStr = '3.14159';
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $_ = (float) $floatStr;
        }
        $times['string → float'] = \microtime(true) - $start;

        // intval()
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $_ = \intval($str);
        }
        $times['intval()'] = \microtime(true) - $start;

        // settype equivalent: bool cast
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $_ = (bool) $i;
        }
        $times['int → bool'] = \microtime(true) - $start;

        return new TypeCastingResult(
            castTimes: $times,
            cycles: $count,
        );
    }
}
