<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Result;

final readonly class CpuResult implements \JsonSerializable
{
    /**
     * @param array<string, float> $mathResults
     * @param array<string, float> $stringResults
     */
    public function __construct(
        public array $mathResults,
        public int $mathCount,
        public array $stringResults,
        public int $stringCount,
        public float $loopsResult,
        public int $loopsCount,
        public float $ifElseResult,
        public int $ifElseCount,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'math' => ['count' => $this->mathCount, 'results' => $this->mathResults],
            'strings' => ['count' => $this->stringCount, 'results' => $this->stringResults],
            'loops' => ['count' => $this->loopsCount, 'result' => $this->loopsResult],
            'if_else' => ['count' => $this->ifElseCount, 'result' => $this->ifElseResult],
        ];
    }
}
