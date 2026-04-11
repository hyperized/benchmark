<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Generic;

class Utility
{
    /**
     * @param array<int|string, string|int|float> $variables
     */
    public static function format(string $string, array $variables): string
    {
        $string = (string) \preg_replace_callback('#\{\}#', function () {
            static $i = 0;
            return '{' . ($i++) . '}';
        }, $string);

        return \str_replace(
            \array_map(fn(int|string $k): string => '{' . $k . '}', \array_keys($variables)),
            \array_map(fn(string|int|float $v): string => (string) $v, \array_values($variables)),
            $string,
        );
    }
}
