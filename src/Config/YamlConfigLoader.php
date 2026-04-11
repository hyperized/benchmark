<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Config;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

class YamlConfigLoader extends FileLoader
{
    public function load(mixed $resource, ?string $type = null): mixed
    {
        $content = \file_get_contents($resource);
        if ($content === false) {
            throw new \RuntimeException('Could not read file: ' . $resource);
        }
        return Yaml::parse($content);
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return \is_string($resource) && 'yml' === \pathinfo(
            $resource,
            PATHINFO_EXTENSION,
        );
    }
}
