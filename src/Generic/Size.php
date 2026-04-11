<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Generic;

class Size
{
    /** @var list<string> */
    private static array $units = [
        'B',
        'KB',
        'MB',
        'GB',
        'TB',
        'PB',
        'EB',
        'ZB',
        'YB',
    ];

    private static string $unitsPattern = 'bkmgtpezy';
    private static string $unitsRegexPattern = '/[^bkmgtpezy]/i';
    private static string $numberRegex = '/[^0-9\.]/';

    public static function bytesToFormat(int $bytes): string
    {
        $power = $bytes > 0 ? (int) \floor(\log($bytes, 1024)) : 0;
        return \number_format($bytes / (1024 ** $power), 2, '.', ',') . ' ' . self::$units[$power];
    }

    public static function formatToBytes(string $format): float
    {
        $unit = (string) \preg_replace(self::$unitsRegexPattern, '', $format);
        $number = (string) \preg_replace(self::$numberRegex, '', $format);
        return $unit !== '' ? \round((float) $number * (1024 ** (int) \stripos(self::$unitsPattern, $unit[0]))) : \round((float) $number);
    }
}
