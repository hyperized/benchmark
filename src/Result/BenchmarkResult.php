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
        public ?MemoryResult $memory,
        public ?NetworkResult $network,
        public ?JsonResult $json,
        public ?RegexResult $regex,
        public ?FileReadResult $fileRead,
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
        if ($this->memory !== null) {
            $data['memory'] = $this->memory;
        }
        if ($this->network !== null) {
            $data['network'] = $this->network;
        }
        if ($this->json !== null) {
            $data['json'] = $this->json;
        }
        if ($this->regex !== null) {
            $data['regex'] = $this->regex;
        }
        if ($this->fileRead !== null) {
            $data['file_read'] = $this->fileRead;
        }
        if ($this->mysql !== null) {
            $data['mysql'] = $this->mysql;
        }

        return $data;
    }
}
