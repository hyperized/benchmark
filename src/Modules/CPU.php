<?php

namespace Hyperized\Benchmark\Modules;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Generic\Table;
use Hyperized\Benchmark\Generic\Visual;

/**
 * Class CPU
 * @package Hyperized\Benchmark\Modules
 *
 * Totally borrowed from: https://github.com/odan/benchmark-php/blob/master/benchmark.php
 */
class CPU
{
    /**
     * @var int
     */
    private static $defaultCount = 99999;
    /**
     * @var array
     */
    private static $mathFunctions = [
        "abs",
        "acos",
        "asin",
        "atan",
        "bindec",
        "floor",
        "exp",
        "sin",
        "tan",
        "pi",
        "is_finite",
        "is_nan",
        "sqrt"
    ];
    /**
     * @var array
     */
    private static $stringFunctions = [
        "addslashes",
        "chunk_split",
        "metaphone",
        "strip_tags",
        "md5",
        "sha1",
        "strtoupper",
        "strtolower",
        "strrev",
        "strlen",
        "soundex",
        "ord"
    ];
    /**
     * @var string
     */
    private static $string = 'the quick brown fox jumps over the lazy dog';

    /**
     * @var \Hyperized\Benchmark\Config\Config
     */
    private $config;

    /**
     * @var array
     */
    private $mathResults = [];
    /**
     * @var array
     */
    private $stringsResults = [];
    /**
     * @var array
     */
    private $loopsResults;
    /**
     * @var array
     */
    private $ifElseResults;

    /**
     * @var
     */
    private $mathCount;
    /**
     * @var
     */
    private $stringsCount;
    /**
     * @var
     */
    private $loopsCount;
    /**
     * @var
     */
    private $ifElseCount;

    /**
     * CPU constructor.
     *
     * @param \Hyperized\Benchmark\Config\Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->configure();

        if ($config->get('benchmark.cpu.enabled')) {
            $this->run();
            $this->render();
        }
    }

    /**
     * Configure
     */
    private function configure()
    {
        $this->mathCount = !is_null($this->config->get('benchmark.cpu.math.count')) ? $this->config->get('benchmark.cpu.math.count') : self::$defaultCount;
        $this->stringsCount = !is_null($this->config->get('benchmark.cpu.strings.count')) ? $this->config->get('benchmark.cpu.strings.count') : self::$defaultCount;
        $this->loopsCount = !is_null($this->config->get('benchmark.cpu.loops.count')) ? $this->config->get('benchmark.cpu.loops.count') : self::$defaultCount;
        $this->ifElseCount = !is_null($this->config->get('benchmark.cpu.ifElse.count')) ? $this->config->get('benchmark.cpu.ifElse.count') : self::$defaultCount;
    }

    /**
     * Run!
     */
    private function run()
    {
        $this->math();
        $this->strings();
        $this->loops();
        $this->ifElse();
    }

    /**
     * Render
     */
    private function render()
    {
        Visual::print('== CPU performance information', "\n");
        Visual::print('Math operation results by function in milliseconds (less is beter), for a total of ' . $this->mathCount . ' cycles:');
        new Table($this->mathResults);
        Visual::print(' ', "\n");
        Visual::print('String operation results by function in milliseconds (less is beter), for a total of ' . $this->stringsCount . ' cycles:');
        new Table($this->stringsResults);
        Visual::print(' ', "\n");
        Visual::print('Loop operation results in milliseconds (less is beter), for a total of ' . $this->loopsCount . ' cycles: ' . $this->loopsResults);
        Visual::print('If/Else operation results in milliseconds (less is beter), for a total of ' . $this->ifElseCount . ' cycles: ' . $this->ifElseResults);
        Visual::print(' ', "\n");
    }

    /**
     * Do Maths!
     */
    private function math()
    {
        foreach (self::$mathFunctions as $function) {
            $this->mathResults['x'][$function] = 0;
            $start = microtime(true);

            for ($i = 0; $i < $this->mathCount; $i++) {
                call_user_func_array($function, array($i));
            }

            $this->mathResults['x'][$function] += (microtime(true) - $start);
        }
    }

    /**
     * Do string operations
     */
    private function strings()
    {
        foreach (self::$stringFunctions as $function) {
            $this->stringsResults['x'][$function] = 0;
            $start = microtime(true);

            for ($i = 0; $i < $this->stringsCount; $i++) {
                call_user_func_array($function, array(self::$string));
            }

            $this->stringsResults['x'][$function] += (microtime(true) - $start);
        }
    }

    /**
     * Loopy loop
     */
    private function loops()
    {
        $start = microtime(true);

        /** @noinspection PhpStatementHasEmptyBodyInspection */
        for ($i = 0; $i < $this->loopsCount; ++$i) {
            ;
        }
        $i = 0;
        while ($i < $this->loopsCount) {
            ++$i;
        }

        $this->loopsResults = (microtime(true) - $start);
    }

    /**
     * ifElseIf really ..
     */
    private function ifElse()
    {
        $start = microtime(true);

        for ($i = 0; $i < $this->ifElseCount; $i++) {
            if ($i == -1) {
                ;
            } elseif ($i == -2) {
                ;
            } else {
                /** @noinspection PhpStatementHasEmptyBodyInspection */
                if ($i == -3) {
                    ;
                }
            }
        }

        $this->ifElseResults = (microtime(true) - $start);
    }
}