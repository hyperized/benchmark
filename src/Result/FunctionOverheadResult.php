<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Result;

final readonly class FunctionOverheadResult implements \JsonSerializable
{
    /**
     * @param array<string, float> $callTimes call type => seconds
     */
    public function __construct(
        public array $callTimes,
        public int $cycles,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'cycles' => $this->cycles,
            'call_times' => $this->callTimes,
        ];
    }
}
