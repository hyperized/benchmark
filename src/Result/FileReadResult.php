<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Result;

final readonly class FileReadResult implements \JsonSerializable
{
    /**
     * @param array<string, float> $readTimes label => seconds
     */
    public function __construct(
        public array $readTimes,
        public int $cycles,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'cycles' => $this->cycles,
            'read_times' => $this->readTimes,
        ];
    }
}
