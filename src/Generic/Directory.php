<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Generic;

class Directory
{
    public static function removeRecursively(string $path): void
    {
        if (!\file_exists($path)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                \rmdir($file->getPathname());
            } else {
                \unlink($file->getPathname());
            }
        }

        \rmdir($path);
    }

    /**
     * @throws \RuntimeException
     */
    public static function create(string $path, int $permissions = 0755): bool
    {
        if (!\file_exists($path) && !\mkdir($path, $permissions) && !\is_dir($path)) {
            throw new \RuntimeException('Could not create directory: ' . $path);
        }
        return true;
    }
}
