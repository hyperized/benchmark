<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Modules;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Generic\Directory;
use Hyperized\Benchmark\Result\DiskResult;

class Disk implements BenchmarkModuleInterface
{
    private static string $path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
    private static string $tmpDirectory = DIRECTORY_SEPARATOR . 'tmp';

    /** @var list<positive-int> */
    private static array $commonBlockSizesBytes = [
        512,
        1024,
        2048,
        4096,
        8192,
        16384,
        32678,
        65536,
    ];

    public function __construct(private readonly Config $config) {}

    public function isEnabled(): bool
    {
        return (bool) $this->config->get('benchmark.disk.enabled');
    }

    public function run(): DiskResult
    {
        $cycles = (int) $this->config->get('benchmark.disk.cycles');
        $initial = \time();
        $tmpDirectoryPath = \realpath(self::$path) . self::$tmpDirectory;

        /** @var array<int, float> $fileCreationTimes */
        $fileCreationTimes = [];

        Directory::create($tmpDirectoryPath);

        foreach (self::$commonBlockSizesBytes as $bytes) {
            $fileCreationTimes[$bytes] = 0.0;
        }

        for ($c = $cycles; $c >= 0; $c--) {
            foreach (self::$commonBlockSizesBytes as $bytes) {
                $prefix = $initial . '_' . $bytes;
                $content = \random_bytes($bytes);

                $start = \microtime(true);

                $file = \tempnam($tmpDirectoryPath, $prefix);
                if ($file !== false) {
                    \file_put_contents($file, $content);
                }

                $fileCreationTimes[$bytes] += (\microtime(true) - $start);
            }
        }

        Directory::removeRecursively($tmpDirectoryPath);

        return new DiskResult(
            fileCreationTimes: $fileCreationTimes,
            cycles: $cycles,
        );
    }
}
