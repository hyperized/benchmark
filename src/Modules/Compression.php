<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Modules;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Result\CompressionResult;

class Compression implements BenchmarkModuleInterface
{
    private static int $defaultCount = 999;

    public function __construct(private readonly Config $config) {}

    public function isEnabled(): bool
    {
        return (bool) $this->config->get('benchmark.compression.enabled');
    }

    public function run(): CompressionResult
    {
        $count = (int) ($this->config->get('benchmark.compression.count') ?? self::$defaultCount);

        $data = \str_repeat('The quick brown fox jumps over the lazy dog. Lorem ipsum dolor sit amet. ', 500);

        /** @var array<string, float> $compressTimes */
        $compressTimes = [];
        /** @var array<string, float> $decompressTimes */
        $decompressTimes = [];
        /** @var array<string, float> $ratios */
        $ratios = [];

        // gzencode / gzdecode
        if (\function_exists('gzencode')) {
            $this->benchmarkCompression(
                'gzip',
                $data,
                $count,
                static fn(string $d): string => \gzencode($d, 6) ?: '',
                static fn(string $d): string => \gzdecode($d) ?: '',
                $compressTimes,
                $decompressTimes,
                $ratios,
            );
        }

        // gzcompress / gzuncompress (zlib)
        if (\function_exists('gzcompress')) {
            $this->benchmarkCompression(
                'zlib',
                $data,
                $count,
                static fn(string $d): string => \gzcompress($d, 6) ?: '',
                static fn(string $d): string => \gzuncompress($d) ?: '',
                $compressTimes,
                $decompressTimes,
                $ratios,
            );
        }

        // gzdeflate / gzinflate (deflate)
        if (\function_exists('gzdeflate')) {
            $this->benchmarkCompression(
                'deflate',
                $data,
                $count,
                static fn(string $d): string => \gzdeflate($d, 6) ?: '',
                static fn(string $d): string => \gzinflate($d) ?: '',
                $compressTimes,
                $decompressTimes,
                $ratios,
            );
        }

        // brotli (if available)
        if (\function_exists('brotli_compress')) {
            $this->benchmarkCompression(
                'brotli',
                $data,
                $count,
                static fn(string $d): string => \brotli_compress($d) ?: '',
                static fn(string $d): string => \brotli_uncompress($d) ?: '',
                $compressTimes,
                $decompressTimes,
                $ratios,
            );
        }

        // LZF (if available)
        if (\function_exists('lzf_compress')) {
            $this->benchmarkCompression(
                'lzf',
                $data,
                $count,
                static fn(string $d): string => \lzf_compress($d) ?: '',
                static fn(string $d): string => \lzf_decompress($d) ?: '',
                $compressTimes,
                $decompressTimes,
                $ratios,
            );
        }

        return new CompressionResult(
            compressTimes: $compressTimes,
            decompressTimes: $decompressTimes,
            ratios: $ratios,
            cycles: $count,
        );
    }

    /**
     * @param callable(string): string $compress
     * @param callable(string): string $decompress
     * @param array<string, float> $compressTimes
     * @param array<string, float> $decompressTimes
     * @param array<string, float> $ratios
     */
    private function benchmarkCompression(
        string $name,
        string $data,
        int $count,
        callable $compress,
        callable $decompress,
        array &$compressTimes,
        array &$decompressTimes,
        array &$ratios,
    ): void {
        $compressed = $compress($data);

        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $compress($data);
        }
        $compressTimes[$name] = \microtime(true) - $start;

        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $decompress($compressed);
        }
        $decompressTimes[$name] = \microtime(true) - $start;

        $originalLen = \strlen($data);
        $compressedLen = \strlen($compressed);
        $ratios[$name] = $originalLen > 0 ? \round(1 - ($compressedLen / $originalLen), 4) : 0.0;
    }
}
