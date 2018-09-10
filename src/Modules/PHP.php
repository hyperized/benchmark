<?php


namespace Hyperized\Benchmark\Modules;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Generic\Size;
use Hyperized\Benchmark\Generic\Visual;


/**
 * Class PHP
 * @package Hyperized\Benchmark\Modules
 */
class PHP
{
    /**
     * @var array
     */
    private $extensions;

    /**
     * @var int
     */
    private $maxUploadBytes;

    /**
     * @var
     */
    private $maxMemoryBytes;

    /**
     * @var
     */
    private $server;

    /**
     * PHP constructor.
     *
     * @param \Hyperized\Benchmark\Config\Config $config
     */
    public function __construct(Config $config)
    {
        if ($config->get('benchmark.php.enabled')) {
            $this->run();
            $this->render();
        }
    }

    /**
     * Run!
     */
    private function run(): void
    {
        $this->extensions = $this->getExtensions();
        $this->maxUploadBytes = $this->getMaxUploadBytes();
        $this->maxMemoryBytes = $this->getMaxMemoryBytes();
        $this->server = $this->getServer();
    }

    /**
     * @return array
     */
    private function getExtensions(): array
    {
        return \get_loaded_extensions();
    }

    /**
     * @return int
     *
     * https://stackoverflow.com/a/25370978/1757763
     */
    private function getMaxUploadBytes(): int
    {
        static $max_size = -1;

        if ($max_size < 0) {
            // Start with post_max_size.
            $post_max_size = Size::formatToBytes(\ini_get('post_max_size'));
            if ($post_max_size > 0) {
                $max_size = $post_max_size;
            }

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $upload_max = Size::formatToBytes(\ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }
        return $max_size;
    }

    /**
     * @return int
     */
    private function getMaxMemoryBytes(): int
    {
        return (int)Size::formatToBytes(\ini_get('memory_limit'));
    }

    /**
     * @return string
     */
    private function getServer(): string
    {
        if (isset($_SERVER['SERVER_SOFTWARE'])) {
            return $_SERVER['SERVER_SOFTWARE'];
        }
        return 'Probably CLI';
    }

    /**
     * Gives output
     */
    private function render(): void
    {
        Visual::print('== Generic PHP information');
        Visual::print('PHP version: ' . $this->getVersion(), "\n");
        Visual::print('Server: ' . $this->server, "\n");
        Visual::print('Maximum execution time: ' . $this->getMaximumExecutionTime() . ' seconds');
        Visual::print('Maximum memory size: ' . Size::bytesToFormat($this->maxMemoryBytes) . ' (' . $this->maxMemoryBytes . ' bytes)', "\n");
        Visual::print('Maximum upload size: ' . Size::bytesToFormat($this->maxUploadBytes) . ' (' . $this->maxUploadBytes . ' bytes)');
        Visual::print('Modules loaded:', "\n");
        foreach ($this->extensions as $extension) {
            Visual::print(' ' . $extension . ' (' . $this->getVersion($extension) . ')', "\n");
        }
        Visual::print(' ', "\n");
    }

    /**
     * @param null $extension
     *
     * @return string
     */
    private function getVersion($extension = null): string
    {
        if ($extension !== null) {
            return \phpversion($extension);
        }
        return PHP_VERSION;
    }

    /**
     * @return string
     */
    private function getMaximumExecutionTime(): string
    {
        return \ini_get('max_execution_time');
    }
}