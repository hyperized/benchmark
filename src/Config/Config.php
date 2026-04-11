<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Config;

use Adbar\Dot;
use Symfony\Component\Config\FileLocator;

class Config
{
    private string $file = 'config.yml';
    private string $directory = 'config';

    /** @var array<string, mixed> */
    private array $config = [];

    public function __construct()
    {
        $locator = new FileLocator([
            __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $this->directory,
        ]);

        try {
            $loader = new YamlConfigLoader($locator);

            $this->config = $loader->load(
                $locator->locate($this->file),
            );
        } catch (\Exception) {
            echo $this->file . ' could not be loaded, please copy ' . $this->directory . '/config.yml.example to ' . $this->directory . '/' . $this->file . "\n";
        }
    }

    public function setFile(string $file): void
    {
        $this->file = $file;
    }

    public function setDirectory(string $directory): void
    {
        $this->directory = $directory;
    }

    public function get(string $field): mixed
    {
        $dot = new Dot($this->config);

        return $dot->get($field);
    }

    /**
     * @return array<string, mixed>
     */
    public function getAll(): array
    {
        return $this->config;
    }
}
