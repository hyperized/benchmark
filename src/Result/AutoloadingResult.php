<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Result;

final readonly class AutoloadingResult implements \JsonSerializable
{
    /**
     * @param array<string, float> $instantiationTimes class => seconds
     */
    public function __construct(
        public array $instantiationTimes,
        public int $cycles,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'cycles' => $this->cycles,
            'instantiation_times' => $this->instantiationTimes,
        ];
    }
}
