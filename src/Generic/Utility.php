<?php


namespace Hyperized\Benchmark\Generic;


class Utility
{
    /**
     * @param string $string
     * @param array $variables
     *
     * https://stackoverflow.com/a/17372566/1757763
     * @return mixed
     */
    public static function format(string $string, array $variables)
    {
        $string = \preg_replace_callback('#\{\}#', function () {
            static $i = 0;
            return '{' . ($i++) . '}';
        }, $string);

        return \str_replace(
            \array_map(function ($k) {
                return '{' . $k . '}';
            }, \array_keys($variables)), \array_values($variables), $string
        );
    }
}