<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Result;

final readonly class CompressionResult implements \JsonSerializable
{
    /**
     * @param array<string, float> $compressTimes algorithm => seconds
     * @param array<string, float> $decompressTimes algorithm => seconds
     * @param array<string, float> $ratios algorithm => compression ratio
     */
    public function __construct(
        public array $compressTimes,
        public array $decompressTimes,
        public array $ratios,
        public int $cycles,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'cycles' => $this->cycles,
            'compress_times' => $this->compressTimes,
            'decompress_times' => $this->decompressTimes,
            'ratios' => $this->ratios,
        ];
    }
}
