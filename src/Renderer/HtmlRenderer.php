<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Renderer;

use Hyperized\Benchmark\Generic\Size;
use Hyperized\Benchmark\Result\BenchmarkResult;
use Hyperized\Benchmark\Result\CpuResult;
use Hyperized\Benchmark\Result\DiskResult;
use Hyperized\Benchmark\Result\MemoryResult;
use Hyperized\Benchmark\Result\MySqlResult;
use Hyperized\Benchmark\Result\PhpResult;

final class HtmlRenderer implements RendererInterface
{
    public function render(BenchmarkResult $result, ?string $reportPath): void
    {
        echo '<!DOCTYPE html>';
        echo '<html lang="en"><head>';
        echo '<meta charset="UTF-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        echo '<title>PHP Benchmark Report</title>';
        echo '<style>' . $this->css() . '</style>';
        echo '</head><body>';

        $this->renderHeader($result);

        echo '<main class="grid">';

        if ($result->php !== null) {
            $this->renderPhp($result->php);
        }
        if ($result->disk !== null) {
            $this->renderDisk($result->disk);
        }
        if ($result->memory !== null) {
            $this->renderMemory($result->memory);
        }
        if ($result->cpu !== null) {
            $this->renderCpu($result->cpu);
        }
        if ($result->mysql !== null) {
            $this->renderMySql($result->mysql);
        }

        echo '</main>';

        $this->renderFooter($result, $reportPath);

        echo '</body></html>';
    }

    private function renderHeader(BenchmarkResult $result): void
    {
        $date = $this->esc($result->timestamp->format('Y-m-d H:i:s'));
        $duration = \sprintf('%.3fs', $result->totalDuration);

        echo '<header class="banner">';
        echo '<h1>PHP Benchmark Report</h1>';
        echo '<div class="meta">';
        echo '<span>' . $date . '</span>';
        echo '<span class="duration">' . $this->esc($duration) . '</span>';
        echo '</div>';
        echo '</header>';
    }

    private function renderPhp(PhpResult $php): void
    {
        echo '<section class="card">';
        echo '<div class="card-title">PHP</div>';
        echo '<div class="card-body">';

        echo '<dl class="kv">';
        $this->dt('PHP Version', $php->phpVersion);
        $this->dt('Server', $php->server);
        $this->dt('Max Memory', Size::bytesToFormat($php->maxMemoryBytes));
        $this->dt('Max Upload', Size::bytesToFormat($php->maxUploadBytes));
        $this->dt('Max Exec Time', $php->maxExecutionTime . 's');
        echo '</dl>';

        echo '<details><summary>' . \count($php->extensions) . ' extensions loaded</summary>';
        echo '<div class="ext-list">';
        foreach ($php->extensions as $ext) {
            echo '<span class="ext">' . $this->esc($ext['name']) . ' <small>' . $this->esc($ext['version']) . '</small></span>';
        }
        echo '</div></details>';

        echo '</div></section>';
    }

    private function renderDisk(DiskResult $disk): void
    {
        echo '<section class="card card-wide">';
        echo '<div class="card-title">Disk I/O</div>';
        echo '<div class="card-body">';
        echo '<p class="subtitle">' . $disk->cycles . ' cycles &middot; seconds (lower is better)</p>';

        echo '<table><thead><tr>';
        foreach ($disk->fileCreationTimes as $blockSize => $_) {
            echo '<th>' . $this->esc(Size::bytesToFormat($blockSize)) . '</th>';
        }
        echo '</tr></thead><tbody><tr>';
        foreach ($disk->fileCreationTimes as $time) {
            echo '<td>' . \sprintf('%.4fs', $time) . '</td>';
        }
        echo '</tr></tbody></table>';

        echo '</div></section>';
    }

    private function renderMemory(MemoryResult $memory): void
    {
        echo '<section class="card card-wide">';
        echo '<div class="card-title">Memory</div>';
        echo '<div class="card-body">';
        echo '<p class="subtitle">' . $memory->cycles . ' cycles &middot; seconds (lower is better)</p>';

        echo '<h3>Time</h3>';
        echo '<table><thead><tr>';
        foreach (\array_keys($memory->allocationTimes) as $name) {
            echo '<th>' . $this->esc($name) . '</th>';
        }
        echo '</tr></thead><tbody><tr>';
        foreach ($memory->allocationTimes as $time) {
            echo '<td>' . \sprintf('%.4fs', $time) . '</td>';
        }
        echo '</tr></tbody></table>';

        echo '<h3>Peak Memory</h3>';
        echo '<table><thead><tr>';
        foreach (\array_keys($memory->peakMemoryUsages) as $name) {
            echo '<th>' . $this->esc($name) . '</th>';
        }
        echo '</tr></thead><tbody><tr>';
        foreach ($memory->peakMemoryUsages as $bytes) {
            echo '<td>' . $this->esc(Size::bytesToFormat($bytes)) . '</td>';
        }
        echo '</tr></tbody></table>';

        echo '</div></section>';
    }

    private function renderCpu(CpuResult $cpu): void
    {
        echo '<section class="card card-wide">';
        echo '<div class="card-title">CPU</div>';
        echo '<div class="card-body">';

        echo '<p class="subtitle">seconds (lower is better)</p>';

        echo '<h3>Math <small>(' . $cpu->mathCount . ' cycles)</small></h3>';
        $this->renderResultTable($cpu->mathResults);

        echo '<h3>Strings <small>(' . $cpu->stringCount . ' cycles)</small></h3>';
        $this->renderResultTable($cpu->stringResults);

        echo '<dl class="kv">';
        $this->dt('Loops', \sprintf('%.6fs', $cpu->loopsResult) . ' (' . $cpu->loopsCount . ' cycles)');
        $this->dt('If/Else', \sprintf('%.6fs', $cpu->ifElseResult) . ' (' . $cpu->ifElseCount . ' cycles)');
        echo '</dl>';

        echo '</div></section>';
    }

    private function renderMySql(MySqlResult $mysql): void
    {
        echo '<section class="card">';
        echo '<div class="card-title">MySQL</div>';
        echo '<div class="card-body">';
        echo '<dl class="kv">';
        $this->dt('Version', (string) $mysql->version);
        $this->dt('Query Time', \sprintf('%.4fs', $mysql->queryTime) . ' (' . $mysql->queryCount . ' cycles, lower is better)');
        echo '</dl>';
        echo '</div></section>';
    }

    private function renderFooter(BenchmarkResult $result, ?string $reportPath): void
    {
        echo '<footer>';
        echo '<span>Total duration: <strong>' . \sprintf('%.3fs', $result->totalDuration) . '</strong></span>';
        if ($reportPath !== null) {
            echo '<span>Report: <code>' . $this->esc($reportPath) . '</code></span>';
        }
        echo '</footer>';
    }

    /**
     * @param array<string, float> $results
     */
    private function renderResultTable(array $results): void
    {
        echo '<table><thead><tr>';
        foreach (\array_keys($results) as $name) {
            echo '<th>' . $this->esc($name) . '</th>';
        }
        echo '</tr></thead><tbody><tr>';
        foreach ($results as $time) {
            echo '<td>' . \sprintf('%.4fs', $time) . '</td>';
        }
        echo '</tr></tbody></table>';
    }

    private function dt(string $key, string $value): void
    {
        echo '<dt>' . $this->esc($key) . '</dt><dd>' . $this->esc($value) . '</dd>';
    }

    private function esc(string $value): string
    {
        return \htmlspecialchars($value, ENT_QUOTES | ENT_HTML5);
    }

    private function css(): string
    {
        return <<<'CSS'
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f0f2f5;
            color: #1a1a2e;
            line-height: 1.6;
        }
        .banner {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #fff;
            padding: 2rem;
            text-align: center;
        }
        .banner h1 { font-size: 1.8rem; font-weight: 600; }
        .banner .meta { margin-top: 0.5rem; opacity: 0.8; display: flex; justify-content: center; gap: 2rem; }
        .banner .duration { font-family: "SF Mono", "Cascadia Code", monospace; color: #4ecca3; }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .card-wide { grid-column: 1 / -1; }
        .card-title {
            background: #1a1a2e;
            color: #4ecca3;
            padding: 0.75rem 1.25rem;
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .card-body { padding: 1.25rem; }
        .card-body h3 { margin: 1rem 0 0.5rem; color: #16213e; font-size: 0.95rem; }
        .card-body h3 small { color: #888; font-weight: 400; }
        .card-body h3:first-child { margin-top: 0; }
        dl.kv { display: grid; grid-template-columns: auto 1fr; gap: 0.3rem 1rem; }
        dl.kv dt { color: #666; font-size: 0.9rem; }
        dl.kv dd { font-family: "SF Mono", "Cascadia Code", monospace; font-size: 0.9rem; color: #1a1a2e; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0.5rem 0 1rem;
            font-size: 0.85rem;
        }
        table th {
            background: #f0f2f5;
            padding: 0.5rem 0.75rem;
            text-align: left;
            font-weight: 600;
            color: #16213e;
            border-bottom: 2px solid #ddd;
        }
        table td {
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid #eee;
            font-family: "SF Mono", "Cascadia Code", monospace;
            color: #2d6a4f;
        }
        table tr:last-child td { border-bottom: none; }
        .subtitle { color: #888; font-size: 0.9rem; margin-bottom: 0.75rem; }
        details { margin-top: 1rem; }
        summary {
            cursor: pointer;
            color: #16213e;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.3rem 0;
        }
        .ext-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
            margin-top: 0.5rem;
        }
        .ext {
            background: #f0f2f5;
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-family: "SF Mono", "Cascadia Code", monospace;
        }
        .ext small { color: #888; }
        footer {
            text-align: center;
            padding: 1.5rem 2rem;
            color: #888;
            font-size: 0.85rem;
            display: flex;
            justify-content: center;
            gap: 2rem;
        }
        footer strong { color: #1a1a2e; }
        footer code { background: #f0f2f5; padding: 0.1rem 0.4rem; border-radius: 3px; }
        @media (max-width: 600px) {
            .grid { grid-template-columns: 1fr; padding: 1rem; gap: 1rem; }
            .banner { padding: 1.5rem 1rem; }
            .banner h1 { font-size: 1.4rem; }
            footer { flex-direction: column; gap: 0.5rem; }
        }
        CSS;
    }
}
