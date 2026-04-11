<?php

declare(strict_types=1);

namespace Hyperized\Benchmark;

use Hyperized\Benchmark\Config\Config;
use Hyperized\Benchmark\Modules\Autoloading;
use Hyperized\Benchmark\Modules\Compression;
use Hyperized\Benchmark\Modules\CPU;
use Hyperized\Benchmark\Modules\Disk;
use Hyperized\Benchmark\Modules\Encryption;
use Hyperized\Benchmark\Modules\FileRead;
use Hyperized\Benchmark\Modules\FunctionOverhead;
use Hyperized\Benchmark\Modules\Hashing;
use Hyperized\Benchmark\Modules\Json;
use Hyperized\Benchmark\Modules\Memory;
use Hyperized\Benchmark\Modules\MySQL;
use Hyperized\Benchmark\Modules\Network;
use Hyperized\Benchmark\Modules\PHP;
use Hyperized\Benchmark\Modules\Regex;
use Hyperized\Benchmark\Modules\Serialization;
use Hyperized\Benchmark\Modules\TypeCasting;
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
        private readonly Network $network,
        private readonly Json $json,
        private readonly Regex $regex,
        private readonly FileRead $fileRead,
        private readonly Serialization $serialization,
        private readonly Hashing $hashing,
        private readonly Encryption $encryption,
        private readonly FunctionOverhead $functionOverhead,
        private readonly TypeCasting $typeCasting,
        private readonly Autoloading $autoloading,
        private readonly Compression $compression,
        private readonly MySQL $mysql,
        private readonly ReportWriter $reportWriter,
    ) {}

    public function execute(): void
    {
        $start = \microtime(true);
        $timestamp = new \DateTimeImmutable();
        $isCli = $this->detectOutputMode() === OutputMode::Cli;

        $modules = [
            'PHP' => $this->php,
            'Disk I/O' => $this->disk,
            'Memory' => $this->memory,
            'Network' => $this->network,
            'JSON' => $this->json,
            'Regex' => $this->regex,
            'File Read' => $this->fileRead,
            'Serialization' => $this->serialization,
            'Hashing' => $this->hashing,
            'Encryption' => $this->encryption,
            'Function Overhead' => $this->functionOverhead,
            'Type Casting' => $this->typeCasting,
            'Object Instantiation' => $this->autoloading,
            'Compression' => $this->compression,
            'CPU' => $this->cpu,
            'MySQL' => $this->mysql,
        ];

        $enabledModules = \array_filter($modules, static fn($m) => $m->isEnabled());
        $total = \count($enabledModules);
        $current = 0;

        /** @var array<string, mixed> $results */
        $results = [];
        foreach ($enabledModules as $name => $module) {
            $current++;
            if ($isCli) {
                echo "\r  \033[1;36m[\033[0m\033[32m{$current}\033[2m/\033[0m{$total}\033[1;36m]\033[0m \033[2mRunning\033[0m \033[1m{$name}\033[0m\033[2m...\033[0m\033[K";
            }
            $results[$name] = $module->run();
        }

        if ($isCli) {
            echo "\r\033[K";
        }

        $phpResult = $results['PHP'] ?? null;
        $diskResult = $results['Disk I/O'] ?? null;
        $memoryResult = $results['Memory'] ?? null;
        $networkResult = $results['Network'] ?? null;
        $jsonResult = $results['JSON'] ?? null;
        $regexResult = $results['Regex'] ?? null;
        $fileReadResult = $results['File Read'] ?? null;
        $serializationResult = $results['Serialization'] ?? null;
        $hashingResult = $results['Hashing'] ?? null;
        $encryptionResult = $results['Encryption'] ?? null;
        $functionOverheadResult = $results['Function Overhead'] ?? null;
        $typeCastingResult = $results['Type Casting'] ?? null;
        $autoloadingResult = $results['Object Instantiation'] ?? null;
        $compressionResult = $results['Compression'] ?? null;
        $cpuResult = $results['CPU'] ?? null;
        $mysqlResult = $results['MySQL'] ?? null;

        $result = new BenchmarkResult(
            timestamp: $timestamp,
            totalDuration: \microtime(true) - $start,
            php: $phpResult,
            cpu: $cpuResult,
            disk: $diskResult,
            memory: $memoryResult,
            network: $networkResult,
            json: $jsonResult,
            regex: $regexResult,
            fileRead: $fileReadResult,
            serialization: $serializationResult,
            hashing: $hashingResult,
            encryption: $encryptionResult,
            functionOverhead: $functionOverheadResult,
            typeCasting: $typeCastingResult,
            autoloading: $autoloadingResult,
            compression: $compressionResult,
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
