<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Result;

final readonly class MemoryResult implements \JsonSerializable
{
    /**
     * @param array<string, float> $allocationTimes operation name => seconds
     * @param array<string, int> $peakMemoryUsages operation name => bytes
     */
    public function __construct(
        public array $allocationTimes,
        public array $peakMemoryUsages,
        public int $cycles,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'cycles' => $this->cycles,
            'allocation_times' => $this->allocationTimes,
            'peak_memory_usages' => $this->peakMemoryUsages,
        ];
    }
}
