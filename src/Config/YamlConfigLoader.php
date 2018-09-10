<?php

namespace Hyperized\Benchmark\Config;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlConfigLoader
 * @package Hyperized\Benchmark\Config
 */
class YamlConfigLoader extends FileLoader
{
    /**
     * @param mixed $resource   The resource
     * @param string|null $type The resource type or null if unknown
     *
     * @return mixed
     */
    public function load($resource, $type = null)
    {
        return Yaml::parse(\file_get_contents($resource));
    }

    /**
     * @param mixed $resource   A resource
     * @param string|null $type The resource type or null if unknown
     *
     * @return bool
     */
    public function supports($resource, $type = null): bool
    {
        return \is_string($resource) && 'yml' === \pathinfo(
                $resource,
                PATHINFO_EXTENSION
            );
    }
}