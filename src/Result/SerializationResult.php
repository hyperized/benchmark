<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Result;

final readonly class SerializationResult implements \JsonSerializable
{
    /**
     * @param array<string, float> $serializeTimes format => seconds
     * @param array<string, float> $unserializeTimes format => seconds
     */
    public function __construct(
        public array $serializeTimes,
        public array $unserializeTimes,
        public int $cycles,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'cycles' => $this->cycles,
            'serialize_times' => $this->serializeTimes,
            'unserialize_times' => $this->unserializeTimes,
        ];
    }
}
