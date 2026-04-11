<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Modules;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Result\SerializationResult;

class Serialization implements BenchmarkModuleInterface
{
    private static int $defaultCount = 2000;

    public function __construct(private readonly Config $config) {}

    public function isEnabled(): bool
    {
        return (bool) $this->config->get('benchmark.serialization.enabled');
    }

    public function run(): SerializationResult
    {
        $count = (int) ($this->config->get('benchmark.serialization.count') ?? self::$defaultCount);

        $payload = $this->buildPayload();
        $phpSerialized = \serialize($payload);
        $jsonEncoded = \json_encode($payload, JSON_THROW_ON_ERROR);
        $igbinarySerialized = \function_exists('igbinary_serialize') ? \igbinary_serialize($payload) : null;
        $msgpackPacked = \function_exists('msgpack_pack') ? \msgpack_pack($payload) : null;

        /** @var array<string, float> $serializeTimes */
        $serializeTimes = [];
        /** @var array<string, float> $unserializeTimes */
        $unserializeTimes = [];

        // PHP serialize
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            \serialize($payload);
        }
        $serializeTimes['serialize'] = \microtime(true) - $start;

        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            \unserialize($phpSerialized);
        }
        $unserializeTimes['unserialize'] = \microtime(true) - $start;

        // JSON
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            \json_encode($payload, JSON_THROW_ON_ERROR);
        }
        $serializeTimes['json_encode'] = \microtime(true) - $start;

        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            \json_decode($jsonEncoded, true, 512, JSON_THROW_ON_ERROR);
        }
        $unserializeTimes['json_decode'] = \microtime(true) - $start;

        // var_export / eval alternative: just var_export
        $start = \microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $_ = \var_export($payload, true);
        }
        $serializeTimes['var_export'] = \microtime(true) - $start;
        $unserializeTimes['var_export'] = 0.0;

        // igbinary (if available)
        if ($igbinarySerialized !== null) {
            $start = \microtime(true);
            for ($i = 0; $i < $count; $i++) {
                \igbinary_serialize($payload);
            }
            $serializeTimes['igbinary'] = \microtime(true) - $start;

            $start = \microtime(true);
            for ($i = 0; $i < $count; $i++) {
                \igbinary_unserialize($igbinarySerialized);
            }
            $unserializeTimes['igbinary'] = \microtime(true) - $start;
        }

        // msgpack (if available)
        if ($msgpackPacked !== null) {
            $start = \microtime(true);
            for ($i = 0; $i < $count; $i++) {
                \msgpack_pack($payload);
            }
            $serializeTimes['msgpack'] = \microtime(true) - $start;

            $start = \microtime(true);
            for ($i = 0; $i < $count; $i++) {
                \msgpack_unpack($msgpackPacked);
            }
            $unserializeTimes['msgpack'] = \microtime(true) - $start;
        }

        return new SerializationResult(
            serializeTimes: $serializeTimes,
            unserializeTimes: $unserializeTimes,
            cycles: $count,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPayload(): array
    {
        $items = [];
        for ($i = 0; $i < 100; $i++) {
            $items[] = [
                'id' => $i,
                'name' => 'item_' . $i,
                'value' => $i * 1.5,
                'tags' => ['alpha', 'beta', 'gamma'],
                'active' => $i % 2 === 0,
            ];
        }

        return ['items' => $items, 'total' => 100];
    }
}
