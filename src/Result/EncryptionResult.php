<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Result;

final readonly class EncryptionResult implements \JsonSerializable
{
    /**
     * @param array<string, float> $encryptTimes cipher => seconds
     * @param array<string, float> $decryptTimes cipher => seconds
     */
    public function __construct(
        public array $encryptTimes,
        public array $decryptTimes,
        public int $cycles,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'cycles' => $this->cycles,
            'encrypt_times' => $this->encryptTimes,
            'decrypt_times' => $this->decryptTimes,
        ];
    }
}
