<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Result;

final readonly class MySqlResult implements \JsonSerializable
{
    public function __construct(
        public int $version,
        public float $queryTime,
        public int $queryCount,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'version' => $this->version,
            'query_time' => $this->queryTime,
            'query_count' => $this->queryCount,
        ];
    }
}
