<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Result;

final readonly class TypeCastingResult implements \JsonSerializable
{
    /**
     * @param array<string, float> $castTimes cast type => seconds
     */
    public function __construct(
        public array $castTimes,
        public int $cycles,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'cycles' => $this->cycles,
            'cast_times' => $this->castTimes,
        ];
    }
}
