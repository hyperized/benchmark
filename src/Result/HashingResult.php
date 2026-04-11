<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Result;

final readonly class HashingResult implements \JsonSerializable
{
    /**
     * @param array<string, float> $hashTimes algorithm => seconds
     * @param array<string, float> $passwordTimes algorithm => seconds
     */
    public function __construct(
        public array $hashTimes,
        public array $passwordTimes,
        public int $hashCycles,
        public int $passwordCycles,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'hash_cycles' => $this->hashCycles,
            'password_cycles' => $this->passwordCycles,
            'hash_times' => $this->hashTimes,
            'password_times' => $this->passwordTimes,
        ];
    }
}
