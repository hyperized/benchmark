<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Modules;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Result\JsonResult;

class Json implements BenchmarkModuleInterface
{
    private static int $defaultCount = 1000;

    public function __construct(private readonly Config $config) {}

    public function isEnabled(): bool
    {
        return (bool) $this->config->get('benchmark.json.enabled');
    }

    public function run(): JsonResult
    {
        $count = (int) ($this->config->get('benchmark.json.count') ?? self::$defaultCount);

        $small = ['id' => 1, 'name' => 'test', 'active' => true];
        $medium = $this->buildMediumPayload();
        $large = $this->buildLargePayload();

        $payloads = [
            'Small' => $small,
            'Medium' => $medium,
            'Large' => $large,
        ];

        /** @var array<string, float> $encodeTimes */
        $encodeTimes = [];
        /** @var array<string, float> $decodeTimes */
        $decodeTimes = [];

        foreach ($payloads as $label => $payload) {
            $encoded = \json_encode($payload, JSON_THROW_ON_ERROR);

            $start = \microtime(true);
            for ($i = 0; $i < $count; $i++) {
                \json_encode($payload, JSON_THROW_ON_ERROR);
            }
            $encodeTimes[$label] = \microtime(true) - $start;

            $start = \microtime(true);
            for ($i = 0; $i < $count; $i++) {
                \json_decode($encoded, true, 512, JSON_THROW_ON_ERROR);
            }
            $decodeTimes[$label] = \microtime(true) - $start;
        }

        return new JsonResult(
            encodeTimes: $encodeTimes,
            decodeTimes: $decodeTimes,
            cycles: $count,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function buildMediumPayload(): array
    {
        $items = [];
        for ($i = 0; $i < 50; $i++) {
            $items[] = [
                'id' => $i,
                'name' => 'item_' . $i,
                'value' => $i * 1.5,
                'tags' => ['alpha', 'beta', 'gamma'],
            ];
        }

        return ['items' => $items, 'total' => 50, 'page' => 1];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildLargePayload(): array
    {
        $items = [];
        for ($i = 0; $i < 500; $i++) {
            $items[] = [
                'id' => $i,
                'uuid' => \bin2hex(\random_bytes(16)),
                'name' => 'item_' . $i,
                'description' => \str_repeat('Lorem ipsum dolor sit amet. ', 5),
                'value' => $i * 1.5,
                'nested' => [
                    'level1' => [
                        'level2' => ['data' => \range(0, 9)],
                    ],
                ],
                'tags' => ['alpha', 'beta', 'gamma', 'delta', 'epsilon'],
                'active' => $i % 2 === 0,
            ];
        }

        return ['items' => $items, 'total' => 500, 'page' => 1, 'pages' => 10];
    }
}
