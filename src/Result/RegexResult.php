<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Result;

final readonly class RegexResult implements \JsonSerializable
{
    /**
     * @param array<string, float> $matchTimes pattern label => seconds
     * @param array<string, float> $replaceTimes pattern label => seconds
     */
    public function __construct(
        public array $matchTimes,
        public array $replaceTimes,
        public int $cycles,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'cycles' => $this->cycles,
            'match_times' => $this->matchTimes,
            'replace_times' => $this->replaceTimes,
        ];
    }
}
