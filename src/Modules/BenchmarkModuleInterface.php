<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Modules;

interface BenchmarkModuleInterface
{
    public function isEnabled(): bool;
}
