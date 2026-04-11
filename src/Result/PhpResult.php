<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Result;

final readonly class PhpResult implements \JsonSerializable
{
    /**
     * @param list<array{name: string, version: string}> $extensions
     */
    public function __construct(
        public string $phpVersion,
        public string $server,
        public int $maxExecutionTime,
        public int $maxMemoryBytes,
        public int $maxUploadBytes,
        public array $extensions,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'php_version' => $this->phpVersion,
            'server' => $this->server,
            'max_execution_time' => $this->maxExecutionTime,
            'max_memory_bytes' => $this->maxMemoryBytes,
            'max_upload_bytes' => $this->maxUploadBytes,
            'extensions' => $this->extensions,
        ];
    }
}
