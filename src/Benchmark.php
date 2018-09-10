<?php

namespace Hyperized\Benchmark;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Modules\CPU;
use Hyperized\Benchmark\Modules\Disk;
use Hyperized\Benchmark\Modules\MySQL;
use Hyperized\Benchmark\Modules\PHP;

/**
 * Class Benchmark
 * @package Hyperized\Benchmark
 */
class Benchmark
{
    /**
     * Benchmark constructor.
     *
     * @param Config $config
     * @param PHP $php
     * @param Disk $disk
     * @param CPU $cpu
     * @param MySQL $mysql
     */
    public function __construct(
        /** @scrutinizer ignore-unused */
        Config $config,
        /** @scrutinizer ignore-unused */
        PHP $php,
        /** @scrutinizer ignore-unused */
        Disk $disk,
        /** @scrutinizer ignore-unused */
        CPU $cpu,
        /** @scrutinizer ignore-unused */
        MySQL $mysql
    )
    {
        // Autowired via PHP DI
    }
}