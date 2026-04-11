<?php

declare(strict_types=1);

namespace Hyperized\Benchmark;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Modules\CPU;
use Hyperized\Benchmark\Modules\Disk;
use Hyperized\Benchmark\Modules\Memory;
use Hyperized\Benchmark\Modules\MySQL;
use Hyperized\Benchmark\Modules\PHP;
use Hyperized\Benchmark\Renderer\CliRenderer;
use Hyperized\Benchmark\Renderer\HtmlRenderer;
use Hyperized\Benchmark\Report\Report;
use Hyperized\Benchmark\Report\ReportWriter;
use Hyperized\Benchmark\Result\BenchmarkResult;

final class Benchmark
{
    public function __construct(
        private readonly Config $config,
        private readonly PHP $php,
        private readonly CPU $cpu,
        private readonly Disk $disk,
        private readonly Memory $memory,
        private readonly MySQL $mysql,
        private readonly ReportWriter $reportWriter,
    ) {}

    public function execute(): void
    {
        $start = \microtime(true);
        $timestamp = new \DateTimeImmutable();

        $phpResult = $this->php->isEnabled() ? $this->php->run() : null;
        $diskResult = $this->disk->isEnabled() ? $this->disk->run() : null;
        $memoryResult = $this->memory->isEnabled() ? $this->memory->run() : null;
        $cpuResult = $this->cpu->isEnabled() ? $this->cpu->run() : null;
        $mysqlResult = $this->mysql->isEnabled() ? $this->mysql->run() : null;

        $result = new BenchmarkResult(
            timestamp: $timestamp,
            totalDuration: \microtime(true) - $start,
            php: $phpResult,
            cpu: $cpuResult,
            disk: $diskResult,
            memory: $memoryResult,
            mysql: $mysqlResult,
        );

        $reportEnabled = (bool) ($this->config->get('benchmark.output.report') ?? true);
        $reportPath = null;

        if ($reportEnabled) {
            $report = new Report(
                result: $result,
                hostname: \gethostname() ?: 'unknown',
                phpSapi: \php_sapi_name() ?: 'unknown',
            );
            $reportPath = $this->reportWriter->write($report);
        }

        $renderer = match ($this->detectOutputMode()) {
            OutputMode::Cli => new CliRenderer(),
            OutputMode::Html => new HtmlRenderer(),
        };

        $renderer->render($result, $reportPath);
    }

    private function detectOutputMode(): OutputMode
    {
        $override = $this->config->get('benchmark.output.format');
        if ($override !== null) {
            return OutputMode::from((string) $override);
        }

        return \php_sapi_name() === 'cli' ? OutputMode::Cli : OutputMode::Html;
    }
}
