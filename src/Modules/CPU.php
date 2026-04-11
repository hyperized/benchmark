<?php

/** @noinspection PhpStatementHasEmptyBodyInspection */

declare(strict_types=1);

namespace Hyperized\Benchmark\Modules;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Result\CpuResult;

class CPU implements BenchmarkModuleInterface
{
    private static int $defaultCount = 99999;

    /** @var list<callable-string> */
    private static array $mathFunctions = [
        'abs',
        'acos',
        'asin',
        'atan',
        'floor',
        'exp',
        'sin',
        'tan',
        'is_finite',
        'is_nan',
        'sqrt',
    ];

    /** @var list<callable-string> Functions that take a string argument */
    private static array $mathStringFunctions = [
        'bindec',
    ];

    /** @var list<callable-string> Functions that take no arguments */
    private static array $mathNoArgFunctions = [
        'pi',
    ];

    /** @var list<callable-string> */
    private static array $stringFunctions = [
        'addslashes',
        'chunk_split',
        'metaphone',
        'strip_tags',
        'md5',
        'sha1',
        'strtoupper',
        'strtolower',
        'strrev',
        'strlen',
        'soundex',
        'ord',
    ];

    private static string $string = 'the quick brown fox jumps over the lazy dog';

    public function __construct(private readonly Config $config) {}

    public function isEnabled(): bool
    {
        return (bool) $this->config->get('benchmark.cpu.enabled');
    }

    public function run(): CpuResult
    {
        $mathCount = (int) ($this->config->get('benchmark.cpu.math.count') ?? self::$defaultCount);
        $stringsCount = (int) ($this->config->get('benchmark.cpu.strings.count') ?? self::$defaultCount);
        $loopsCount = (int) ($this->config->get('benchmark.cpu.loops.count') ?? self::$defaultCount);
        $ifElseCount = (int) ($this->config->get('benchmark.cpu.ifElse.count') ?? self::$defaultCount);

        return new CpuResult(
            mathResults: $this->math($mathCount),
            mathCount: $mathCount,
            stringResults: $this->strings($stringsCount),
            stringCount: $stringsCount,
            loopsResult: $this->loops($loopsCount),
            loopsCount: $loopsCount,
            ifElseResult: $this->ifElse($ifElseCount),
            ifElseCount: $ifElseCount,
        );
    }

    /**
     * @return array<string, float>
     */
    private function math(int $count): array
    {
        $results = [];

        foreach (self::$mathFunctions as $function) {
            $callable = $function(...);
            $start = \microtime(true);
            for ($i = 0; $i < $count; $i++) {
                $callable($i);
            }
            $results[$function] = \microtime(true) - $start;
        }

        foreach (self::$mathStringFunctions as $function) {
            $callable = $function(...);
            $start = \microtime(true);
            for ($i = 0; $i < $count; $i++) {
                $callable((string) $i);
            }
            $results[$function] = \microtime(true) - $start;
        }

        foreach (self::$mathNoArgFunctions as $function) {
            $callable = $function(...);
            $start = \microtime(true);
            for ($i = 0; $i < $count; $i++) {
                $callable();
            }
            $results[$function] = \microtime(true) - $start;
        }

        return $results;
    }

    /**
     * @return array<string, float>
     */
    private function strings(int $count): array
    {
        $results = [];

        foreach (self::$stringFunctions as $function) {
            $callable = $function(...);
            $start = \microtime(true);
            for ($i = 0; $i < $count; $i++) {
                $callable(self::$string);
            }
            $results[$function] = \microtime(true) - $start;
        }

        return $results;
    }

    private function loops(int $count): float
    {
        $start = \microtime(true);

        for ($i = 0; $i < $count; ++$i) {
            ;
        }
        $i = 0;
        while ($i < $count) {
            ++$i;
        }

        return \microtime(true) - $start;
    }

    private function ifElse(int $count): float
    {
        $start = \microtime(true);

        for ($i = 0; $i < $count; $i++) {
            if ($i === -1) { /** @phpstan-ignore identical.alwaysFalse */
                ;
            } elseif ($i === -2) { /** @phpstan-ignore identical.alwaysFalse */
                ;
            } elseif ($i === -3) { /** @phpstan-ignore identical.alwaysFalse */
                ;
            }
        }

        return \microtime(true) - $start;
    }
}
