<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Result;

final readonly class JsonResult implements \JsonSerializable
{
    /**
     * @param array<string, float> $encodeTimes label => seconds
     * @param array<string, float> $decodeTimes label => seconds
     */
    public function __construct(
        public array $encodeTimes,
        public array $decodeTimes,
        public int $cycles,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'cycles' => $this->cycles,
            'encode_times' => $this->encodeTimes,
            'decode_times' => $this->decodeTimes,
        ];
    }
}
