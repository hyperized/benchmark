<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Result;

final readonly class NetworkResult implements \JsonSerializable
{
    /**
     * @param array<string, float> $dnsResolutionTimes hostname => seconds
     * @param array<string, float> $connectionTimes host:port => seconds
     */
    public function __construct(
        public array $dnsResolutionTimes,
        public array $connectionTimes,
        public int $cycles,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'cycles' => $this->cycles,
            'dns_resolution_times' => $this->dnsResolutionTimes,
            'connection_times' => $this->connectionTimes,
        ];
    }
}
