<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Modules;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Result\HashingResult;

class Hashing implements BenchmarkModuleInterface
{
    private static int $defaultHashCount = 99999;
    private static int $defaultPasswordCount = 10;

    /** @var list<string> */
    private static array $hashAlgorithms = [
        'md5',
        'sha1',
        'sha256',
        'sha512',
        'xxh3',
        'crc32',
    ];

    public function __construct(private readonly Config $config) {}

    public function isEnabled(): bool
    {
        return (bool) $this->config->get('benchmark.hashing.enabled');
    }

    public function run(): HashingResult
    {
        $hashCount = (int) ($this->config->get('benchmark.hashing.hashCount') ?? self::$defaultHashCount);
        $passwordCount = (int) ($this->config->get('benchmark.hashing.passwordCount') ?? self::$defaultPasswordCount);

        $data = \str_repeat('The quick brown fox jumps over the lazy dog. ', 20);

        /** @var array<string, float> $hashTimes */
        $hashTimes = [];
        foreach (self::$hashAlgorithms as $algo) {
            $start = \microtime(true);
            for ($i = 0; $i < $hashCount; $i++) {
                $_ = \hash($algo, $data);
            }
            $hashTimes[$algo] = \microtime(true) - $start;
        }

        // hash_hmac
        $key = 'benchmark-secret-key';
        $start = \microtime(true);
        for ($i = 0; $i < $hashCount; $i++) {
            $_ = \hash_hmac('sha256', $data, $key);
        }
        $hashTimes['hmac-sha256'] = \microtime(true) - $start;

        /** @var array<string, float> $passwordTimes */
        $passwordTimes = [];

        // bcrypt
        $start = \microtime(true);
        for ($i = 0; $i < $passwordCount; $i++) {
            \password_hash('benchmark', PASSWORD_BCRYPT, ['cost' => 10]);
        }
        $passwordTimes['bcrypt'] = \microtime(true) - $start;

        // argon2id
        $start = \microtime(true);
        for ($i = 0; $i < $passwordCount; $i++) {
            \password_hash('benchmark', PASSWORD_ARGON2ID);
        }
        $passwordTimes['argon2id'] = \microtime(true) - $start;

        return new HashingResult(
            hashTimes: $hashTimes,
            passwordTimes: $passwordTimes,
            hashCycles: $hashCount,
            passwordCycles: $passwordCount,
        );
    }
}
