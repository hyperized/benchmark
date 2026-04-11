<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Modules;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Result\RegexResult;

class Regex implements BenchmarkModuleInterface
{
    private static int $defaultCount = 99999;

    private static string $subject = 'The quick brown fox jumps over 42 lazy dogs at 2026-04-11T14:30:00Z with email user@example.com';

    /** @var array<string, string> label => pattern */
    private static array $patterns = [
        'Literal' => '/quick/',
        'Case-insensitive' => '/QUICK/i',
        'Character class' => '/[a-z]+/',
        'Quantifier' => '/\w{3,8}/',
        'Alternation' => '/fox|dog|cat/',
        'Anchor' => '/^The.*dogs/',
        'Capture group' => '/(\w+)@(\w+)\.(\w+)/',
        'Lookahead' => '/\w+(?=@)/',
        'Date pattern' => '/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z/',
        'Email' => '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/',
    ];

    public function __construct(private readonly Config $config) {}

    public function isEnabled(): bool
    {
        return (bool) $this->config->get('benchmark.regex.enabled');
    }

    public function run(): RegexResult
    {
        $count = (int) ($this->config->get('benchmark.regex.count') ?? self::$defaultCount);

        /** @var array<string, float> $matchTimes */
        $matchTimes = [];
        /** @var array<string, float> $replaceTimes */
        $replaceTimes = [];

        foreach (self::$patterns as $label => $pattern) {
            $start = \microtime(true);
            for ($i = 0; $i < $count; $i++) {
                \preg_match($pattern, self::$subject);
            }
            $matchTimes[$label] = \microtime(true) - $start;

            $start = \microtime(true);
            for ($i = 0; $i < $count; $i++) {
                \preg_replace($pattern, '', self::$subject);
            }
            $replaceTimes[$label] = \microtime(true) - $start;
        }

        return new RegexResult(
            matchTimes: $matchTimes,
            replaceTimes: $replaceTimes,
            cycles: $count,
        );
    }
}
