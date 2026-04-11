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
        public ?SerializationResult $serialization,
        public ?HashingResult $hashing,
        public ?EncryptionResult $encryption,
        public ?FunctionOverheadResult $functionOverhead,
        public ?TypeCastingResult $typeCasting,
        public ?AutoloadingResult $autoloading,
        public ?CompressionResult $compression,
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
        if ($this->serialization !== null) {
            $data['serialization'] = $this->serialization;
        }
        if ($this->hashing !== null) {
            $data['hashing'] = $this->hashing;
        }
        if ($this->encryption !== null) {
            $data['encryption'] = $this->encryption;
        }
        if ($this->functionOverhead !== null) {
            $data['function_overhead'] = $this->functionOverhead;
        }
        if ($this->typeCasting !== null) {
            $data['type_casting'] = $this->typeCasting;
        }
        if ($this->autoloading !== null) {
            $data['autoloading'] = $this->autoloading;
        }
        if ($this->compression !== null) {
            $data['compression'] = $this->compression;
        }
        if ($this->mysql !== null) {
            $data['mysql'] = $this->mysql;
        }

        return $data;
    }
}
