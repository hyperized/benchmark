<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Modules;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Generic\Size;
use Hyperized\Benchmark\Result\PhpResult;

class PHP implements BenchmarkModuleInterface
{
    public function __construct(private readonly Config $config) {}

    public function isEnabled(): bool
    {
        return (bool) $this->config->get('benchmark.php.enabled');
    }

    public function run(): PhpResult
    {
        $extensions = [];
        foreach (\get_loaded_extensions() as $ext) {
            $extensions[] = ['name' => $ext, 'version' => \phpversion($ext) ?: ''];
        }

        return new PhpResult(
            phpVersion: PHP_VERSION,
            server: $_SERVER['SERVER_SOFTWARE'] ?? 'CLI',
            maxExecutionTime: (int) \ini_get('max_execution_time'),
            maxMemoryBytes: (int) Size::formatToBytes((string) \ini_get('memory_limit')),
            maxUploadBytes: $this->getMaxUploadBytes(),
            extensions: $extensions,
        );
    }

    private function getMaxUploadBytes(): int
    {
        $max_size = -1;

        $post_max_size = Size::formatToBytes((string) \ini_get('post_max_size'));
        if ($post_max_size > 0) {
            $max_size = $post_max_size;
        }

        $upload_max = Size::formatToBytes((string) \ini_get('upload_max_filesize'));
        if ($upload_max > 0 && $upload_max < $max_size) {
            $max_size = $upload_max;
        }

        return (int) $max_size;
    }
}
