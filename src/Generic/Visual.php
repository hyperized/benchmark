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
     */
    static function print(string $input, $delimiter = "\n\n")
    {
        print_r($input . $delimiter);
    }
}