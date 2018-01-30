<?php

namespace Hyperized\Benchmark\Generic;


/**
 * Class Size
 * @package Hyperized\Benchmark\Generic
 */
class Size
{
    /**
     * @var array
     */
    private static $units = [
        'B',
        'KB',
        'MB',
        'GB',
        'TB',
        'PB',
        'EB',
        'ZB',
        'YB'
    ];

    /**
     * @var string
     */
    private static $unitsPattern = 'bkmgtpezy';
    /**
     * @var string
     */
    private static $unitsRegexPattern = '/[^bkmgtpezy]/i';
    /**
     * @var string
     */
    private static $numberRegex = '/[^0-9\.]/';

    /**
     * @param string $path
     *
     * @return string
     *
     * https://stackoverflow.com/a/11860664/1757763
     */
    static function bytesToFormat(int $bytes): string
    {
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        return number_format($bytes / pow(1024, $power), 2, '.', ',') . ' ' . static::$units[$power];
    }

    /**
     * @param string $format
     *
     * @return int
     *
     * https://stackoverflow.com/a/25370978/1757763
     */
    static function formatToBytes(string $format): int
    {
        $unit = preg_replace(static::$unitsRegexPattern, '', $format);
        $format = preg_replace(static::$numberRegex, '', $format);
        return $unit ? round($format * pow(1024, stripos(static::$unitsPattern, $unit[0]))) : round($format);
    }
}