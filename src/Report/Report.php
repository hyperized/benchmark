<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Report;

use Hyperized\Benchmark\Result\BenchmarkResult;

final readonly class Report implements \JsonSerializable
{
    public function __construct(
        public BenchmarkResult $result,
        public string $hostname,
        public string $phpSapi,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'generated_at' => $this->result->timestamp->format('c'),
            'hostname' => $this->hostname,
            'sapi' => $this->phpSapi,
            'duration_seconds' => $this->result->totalDuration,
            'modules' => [
                'php' => $this->result->php,
                'cpu' => $this->result->cpu,
                'disk' => $this->result->disk,
                'mysql' => $this->result->mysql,
            ],
        ];
    }
}
