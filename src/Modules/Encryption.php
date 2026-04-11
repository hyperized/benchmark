<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Modules;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Result\EncryptionResult;

class Encryption implements BenchmarkModuleInterface
{
    private static int $defaultCount = 9999;

    /** @var list<string> */
    private static array $ciphers = [
        'aes-128-cbc',
        'aes-256-cbc',
        'aes-128-gcm',
        'aes-256-gcm',
        'chacha20-poly1305',
    ];

    public function __construct(private readonly Config $config) {}

    public function isEnabled(): bool
    {
        return (bool) $this->config->get('benchmark.encryption.enabled');
    }

    public function run(): EncryptionResult
    {
        $count = (int) ($this->config->get('benchmark.encryption.count') ?? self::$defaultCount);

        $data = \str_repeat('The quick brown fox jumps over the lazy dog. ', 20);

        /** @var array<string, float> $encryptTimes */
        $encryptTimes = [];
        /** @var array<string, float> $decryptTimes */
        $decryptTimes = [];

        $availableCiphers = \openssl_get_cipher_methods();

        foreach (self::$ciphers as $cipher) {
            if (!\in_array($cipher, $availableCiphers, true)) {
                continue;
            }

            $ivLen = \openssl_cipher_iv_length($cipher);
            $iv = $ivLen > 0 ? \random_bytes($ivLen) : '';
            $key = \random_bytes(32);
            $tag = '';

            $isAead = \str_contains($cipher, 'gcm') || \str_contains($cipher, 'poly1305');

            // Encrypt
            $start = \microtime(true);
            for ($i = 0; $i < $count; $i++) {
                if ($isAead) {
                    \openssl_encrypt($data, $cipher, $key, 0, $iv, $tag);
                } else {
                    \openssl_encrypt($data, $cipher, $key, 0, $iv);
                }
            }
            $encryptTimes[$cipher] = \microtime(true) - $start;

            // Get encrypted data for decryption benchmark
            if ($isAead) {
                $encrypted = \openssl_encrypt($data, $cipher, $key, 0, $iv, $tag);
            } else {
                $encrypted = \openssl_encrypt($data, $cipher, $key, 0, $iv);
            }

            if ($encrypted === false) {
                continue;
            }

            // Decrypt
            $start = \microtime(true);
            for ($i = 0; $i < $count; $i++) {
                if ($isAead) {
                    \openssl_decrypt($encrypted, $cipher, $key, 0, $iv, $tag);
                } else {
                    \openssl_decrypt($encrypted, $cipher, $key, 0, $iv);
                }
            }
            $decryptTimes[$cipher] = \microtime(true) - $start;
        }

        return new EncryptionResult(
            encryptTimes: $encryptTimes,
            decryptTimes: $decryptTimes,
            cycles: $count,
        );
    }
}
