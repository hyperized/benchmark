<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Renderer;

use Hyperized\Benchmark\Result\BenchmarkResult;

interface RendererInterface
{
    public function render(BenchmarkResult $result, ?string $reportPath): void;
}
