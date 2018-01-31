<?php


namespace Hyperized\Benchmark\Generic;


/**
 * Class Visual
 * @package Hyperized\Benchmark\Generic
 */
class Visual
{
    /**
     * @param string $input
     * @param string $delimiter
     */
    static function print(string $input, $delimiter = "\n\n")
    {q
        print_r($input . $delimiter);
    }
}