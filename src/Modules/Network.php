<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Modules;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Result\NetworkResult;

class Network implements BenchmarkModuleInterface
{
    private static int $defaultCycles = 3;

    /** @var list<string> */
    private static array $defaultHosts = [
        'google.com',
        'cloudflare.com',
        'github.com',
    ];

    public function __construct(private readonly Config $config) {}

    public function isEnabled(): bool
    {
        return (bool) $this->config->get('benchmark.network.enabled');
    }

    public function run(): NetworkResult
    {
        $cycles = (int) ($this->config->get('benchmark.network.cycles') ?? self::$defaultCycles);

        /** @var list<string>|null $configHosts */
        $configHosts = $this->config->get('benchmark.network.hosts');
        $hosts = $configHosts ?? self::$defaultHosts;

        /** @var array<string, float> $dnsTimes */
        $dnsTimes = [];
        /** @var array<string, float> $connectionTimes */
        $connectionTimes = [];

        foreach ($hosts as $host) {
            $dnsTimes[$host] = $this->benchmarkDns($host, $cycles);
            $connectionTimes[$host] = $this->benchmarkConnection($host, 80, $cycles);
        }

        return new NetworkResult(
            dnsResolutionTimes: $dnsTimes,
            connectionTimes: $connectionTimes,
            cycles: $cycles,
        );
    }

    private function benchmarkDns(string $host, int $cycles): float
    {
        $total = 0.0;
        for ($i = 0; $i < $cycles; $i++) {
            $start = \microtime(true);
            $_ = \gethostbyname($host);
            $total += \microtime(true) - $start;
        }

        return $total / $cycles;
    }

    private function benchmarkConnection(string $host, int $port, int $cycles): float
    {
        $total = 0.0;
        for ($i = 0; $i < $cycles; $i++) {
            $start = \microtime(true);
            $socket = @\fsockopen($host, $port, $errno, $errstr, 2);
            $total += \microtime(true) - $start;

            if ($socket !== false) {
                \fclose($socket);
            }
        }

        return $total / $cycles;
    }
}
