<?php

namespace Hyperized\Benchmark\Modules;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Generic\Utility;
use Hyperized\Benchmark\Generic\Visual;

/**
 * Class MySQL
 * @package Hyperized\Benchmark\Modules
 */
class MySQL
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var
     */
    private $connection;
    /**
     * @var string
     */
    private $benchmarkQuery = 'SELECT BENCHMARK({},ENCODE(\'{}\',RAND()));';
    /**
     * @var string
     */
    private $benchmarkText = 'hello';
    /**
     * @var
     */
    private $version;
    /**
     * @var
     */
    private $queryResults;

    /**
     * MySQL constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;

        if ($config->get('benchmark.mysql.enabled')) {
            $this->configure();
            $this->run();
            $this->render();
        }
    }

    /**
     * Configure
     */
    private function configure(): void
    {
        $this->connection = \mysqli_connect(
            $this->config->get('benchmark.mysql.hostname'),
            $this->config->get('benchmark.mysql.username'),
            $this->config->get('benchmark.mysql.password'),
            $this->config->get('benchmark.mysql.database')
        );
    }

    /**
     * Run
     */
    private function run(): void
    {
        $this->getVersion();
        $this->encodeRand();
    }

    /**
     * Obtain MySQL version
     */
    private function getVersion(): void
    {
        $this->version = \mysqli_get_server_version($this->connection);
    }

    /**
     * Run encode with Random query
     */
    private function encodeRand(): void
    {
        $query = Utility::format($this->benchmarkQuery, [
            $this->config->get('benchmark.mysql.count'),
            $this->benchmarkText
        ]);

        $start = \microtime(true);

        \mysqli_query($this->connection, $query);

        $this->queryResults = (\microtime(true) - $start);

    }

    /**
     * Render
     */
    private function render(): void
    {
        Visual::print('== MySQL performance information', "\n");
        Visual::print('MySQL version: ' . $this->version, "\n");
        Visual::print('Query (Encode + random) operation results in milliseconds (less is better), for a total of ' . $this->config->get('benchmark.mysql.count') . ' cycles: ' . $this->queryResults);
    }
}