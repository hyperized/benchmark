<?php


namespace Hyperized\Benchmark\Modules;


use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Generic\Directory;
use Hyperized\Benchmark\Generic\Table;
use Hyperized\Benchmark\Generic\Visual;

/**
 * Class Disk
 * @package Hyperized\Benchmark\Modules
 */
class Disk
{
    /**
     * @var int
     */
    private $initial;
    /**
     * @var
     */
    private $tmpDirectoryPath;
    /**
     * @var array
     */
    private $counterFileCreation = [];
    /**
     * @var int
     */
    private $cycles = 1;

    /**
     * @var string
     */
    private static $path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
    /**
     * @var string
     */
    private static $tmpDirectory = DIRECTORY_SEPARATOR . 'tmp';

    /**
     * @var array
     */
    private static $commonBlockSizesBytes = [
        512,
        1024,
        2048,
        4096,
        8192,
        16384,
        32678,
        65536,
    ];

    /**
     * Disk constructor.
     *
     * @param \Hyperized\Benchmark\Config\Config $config
     */
    public function __construct(Config $config)
    {
        if ($config->get('benchmark.disk.enabled')) {
            $this->cycles = $config->get('benchmark.disk.cycles');
            $this->run();
            $this->render();
        }
    }

    /**
     * Render
     */
    private function render()
    {
        Visual::print("== Disk performance information", "\n");
        Visual::print('Results sorted by file size (in bytes) in milliseconds (less is better), for a total of ' . $this->cycles . " cycles:", "\n");
        new Table($this->counterFileCreation);
        Visual::print(' ', "\n");
    }

    /**
     * Run!
     */
    private function run()
    {
        $this->initial = \time();
        $this->tmpDirectoryPath = \realpath(self::$path) . self::$tmpDirectory;

        try {
            // Create subdirectory
            Directory::create($this->tmpDirectoryPath);

            foreach (self::$commonBlockSizesBytes as $bytes) {
                $this->counterFileCreation['Run'][$bytes] = 0;
            }

            for ($c = $this->cycles; $c >= 0; $c--) {
                // Generate files with different block sizes
                foreach (self::$commonBlockSizesBytes as $bytes) {
                    $prefix = $this->initial . '_' . $bytes;
                    $content = $this->getRandomBytes($bytes);

                    // Start the timer (measure only disk interaction, not string generation etc)
                    $start = \microtime(true);

                    $file = \tempnam($this->tmpDirectoryPath, $prefix);
                    \file_put_contents($file, $content);

                    // Stop timer & append time to timer array with this block size
                    $this->counterFileCreation['Run'][$bytes] += (\microtime(true) - $start);
                }
            }

            // Clean up
            Directory::removeRecursively($this->tmpDirectoryPath);
        } catch (\Exception $e) {
            Visual::print($e);
        }
    }

    /**
     * @param $bytes
     *
     * @return string
     * @throws \Exception
     */
    private function getRandomBytes($bytes): string
    {
        return random_bytes($bytes);
    }
}