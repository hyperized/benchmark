<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Result;

final readonly class BenchmarkResult implements \JsonSerializable
{
    public function __construct(
        public \DateTimeImmutable $timestamp,
        public float $totalDuration,
        public ?PhpResult $php,
        public ?CpuResult $cpu,
        public ?DiskResult $disk,
        public ?MySqlResult $mysql,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $data = [
            'timestamp' => $this->timestamp->format('c'),
            'total_duration' => $this->totalDuration,
        ];

        if ($this->php !== null) {
            $data['php'] = $this->php;
        }
        if ($this->cpu !== null) {
            $data['cpu'] = $this->cpu;
        }
        if ($this->disk !== null) {
            $data['disk'] = $this->disk;
        }
        if ($this->mysql !== null) {
            $data['mysql'] = $this->mysql;
        }

        return $data;
    }
}
