<?php


namespace Hyperized\Benchmark\Generic;


/**
 * Class Directory
 * @package Hyperized\Benchmark\Generic
 */
class Directory
{
    /**
     * @var string
     */
    private static $rootPath = '.';
    /**
     * @var string
     */
    private static $parentPath = '..';

    /**
     * @param $path
     *
     * http://php.net/manual/en/function.rmdir.php#119949
     */
    public static function removeRecursively($path) {
        if (\file_exists($path)) {
            $dir = \opendir($path);
            if(\is_resource($dir)) {
                while (false !== ($file = \readdir($dir))) {
                    if (($file != self::$rootPath) && ($file != self::$parentPath)) {
                        $full = $path . DIRECTORY_SEPARATOR . $file;
                        if (\is_dir($full)) {
                            Directory::removeRecursively($full);
                        } else {
                            \unlink($full);
                        }
                    }
                }
                \closedir($dir);
            }
            \rmdir($path);
        }
    }

    /**
     * @param $path
     * @param $permissions
     *
     * @return bool
     * @throws \Exception
     */
    public static function create($path, $permissions = 0755): bool
    {
        if(!\file_exists($path))
        {
            if (!\mkdir($path, $permissions)) {
                throw new \Exception('Could not create directory: ' . $path);
            }
        }
        return true;
    }
}