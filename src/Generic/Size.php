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
     * @param int $bytes
     *
     * @return string
     *
     * https://stackoverflow.com/a/11860664/1757763
     */
    public static function bytesToFormat(int $bytes): string
    {
        $power = $bytes > 0 ? \floor(\log($bytes, 1024)) : 0;
        return \number_format($bytes / (1024 ** $power), 2, '.', ',') . ' ' . self::$units[$power];
    }

    /**
     * @param string $format
     *
     * @return float
     *
     * https://stackoverflow.com/a/25370978/1757763
     */
    public static function formatToBytes(string $format): float
    {
        $unit = \preg_replace(self::$unitsRegexPattern, '', $format);
        $format = \preg_replace(self::$numberRegex, '', $format);
        return $unit ? \round($format * (1024 ** \stripos(self::$unitsPattern, $unit[0]))) : \round($format);
    }
}