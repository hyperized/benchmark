<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Result;

final readonly class DiskResult implements \JsonSerializable
{
    /**
     * @param array<int, float> $fileCreationTimes block size in bytes => total seconds
     */
    public function __construct(
        public array $fileCreationTimes,
        public int $cycles,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'cycles' => $this->cycles,
            'file_creation_times' => $this->fileCreationTimes,
        ];
    }
}
