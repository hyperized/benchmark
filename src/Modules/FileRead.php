<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Modules;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Generic\Directory;
use Hyperized\Benchmark\Generic\Size;
use Hyperized\Benchmark\Result\FileReadResult;

class FileRead implements BenchmarkModuleInterface
{
    private static string $path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
    private static string $tmpDirectory = DIRECTORY_SEPARATOR . 'tmp';

    /** @var list<positive-int> */
    private static array $fileSizes = [
        1024,
        16384,
        262144,
        1048576,
        4194304,
    ];

    public function __construct(private readonly Config $config) {}

    public function isEnabled(): bool
    {
        return (bool) $this->config->get('benchmark.fileRead.enabled');
    }

    public function run(): FileReadResult
    {
        $cycles = (int) ($this->config->get('benchmark.fileRead.cycles') ?? 100);
        $tmpDirectoryPath = \realpath(self::$path) . self::$tmpDirectory;

        Directory::create($tmpDirectoryPath);

        /** @var array<string, float> $readTimes */
        $readTimes = [];

        foreach (self::$fileSizes as $size) {
            $label = Size::bytesToFormat($size);
            $filePath = $tmpDirectoryPath . DIRECTORY_SEPARATOR . 'read_' . $size;

            \file_put_contents($filePath, \random_bytes($size));

            $total = 0.0;
            for ($i = 0; $i < $cycles; $i++) {
                \clearstatcache(true, $filePath);
                $start = \microtime(true);
                \file_get_contents($filePath);
                $total += \microtime(true) - $start;
            }

            $readTimes[$label] = $total;
        }

        Directory::removeRecursively($tmpDirectoryPath);

        return new FileReadResult(
            readTimes: $readTimes,
            cycles: $cycles,
        );
    }
}
