<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Modules;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Generic\Utility;
use Hyperized\Benchmark\Result\MySqlResult;

class MySQL implements BenchmarkModuleInterface
{
    private static string $benchmarkQuery = 'SELECT BENCHMARK({},SHA2(\'{}\',256));';
    private static string $benchmarkText = 'hello';

    public function __construct(private readonly Config $config) {}

    public function isEnabled(): bool
    {
        return (bool) $this->config->get('benchmark.mysql.enabled');
    }

    public function run(): MySqlResult
    {
        $connection = new \mysqli(
            (string) $this->config->get('benchmark.mysql.hostname'),
            (string) $this->config->get('benchmark.mysql.username'),
            (string) $this->config->get('benchmark.mysql.password'),
            (string) $this->config->get('benchmark.mysql.database'),
        );

        if ($connection->connect_error) {
            throw new \RuntimeException('MySQL connection failed: ' . $connection->connect_error);
        }

        try {
            $version = $connection->server_version;
            $queryCount = (int) $this->config->get('benchmark.mysql.count');

            $query = Utility::format(self::$benchmarkQuery, [
                (string) $queryCount,
                self::$benchmarkText,
            ]);

            $start = \microtime(true);
            $connection->query($query);
            $queryTime = \microtime(true) - $start;

            return new MySqlResult(
                version: $version,
                queryTime: $queryTime,
                queryCount: $queryCount,
            );
        } finally {
            $connection->close();
        }
    }
}
