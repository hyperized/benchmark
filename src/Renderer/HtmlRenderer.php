<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Renderer;

use Hyperized\Benchmark\Generic\Size;
use Hyperized\Benchmark\Result\BenchmarkResult;
use Hyperized\Benchmark\Result\CpuResult;
use Hyperized\Benchmark\Result\DiskResult;
use Hyperized\Benchmark\Result\AutoloadingResult;
use Hyperized\Benchmark\Result\CompressionResult;
use Hyperized\Benchmark\Result\EncryptionResult;
use Hyperized\Benchmark\Result\FileReadResult;
use Hyperized\Benchmark\Result\FunctionOverheadResult;
use Hyperized\Benchmark\Result\HashingResult;
use Hyperized\Benchmark\Result\JsonResult;
use Hyperized\Benchmark\Result\MemoryResult;
use Hyperized\Benchmark\Result\MySqlResult;
use Hyperized\Benchmark\Result\NetworkResult;
use Hyperized\Benchmark\Result\PhpResult;
use Hyperized\Benchmark\Result\RegexResult;
use Hyperized\Benchmark\Result\SerializationResult;
use Hyperized\Benchmark\Result\TypeCastingResult;

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
        if ($result->network !== null) {
            $this->renderNetwork($result->network);
        }
        if ($result->json !== null) {
            $this->renderJson($result->json);
        }
        if ($result->regex !== null) {
            $this->renderRegex($result->regex);
        }
        if ($result->fileRead !== null) {
            $this->renderFileRead($result->fileRead);
        }
        if ($result->serialization !== null) {
            $this->renderSerialization($result->serialization);
        }
        if ($result->hashing !== null) {
            $this->renderHashing($result->hashing);
        }
        if ($result->encryption !== null) {
            $this->renderEncryption($result->encryption);
        }
        if ($result->functionOverhead !== null) {
            $this->renderFunctionOverhead($result->functionOverhead);
        }
        if ($result->typeCasting !== null) {
            $this->renderTypeCasting($result->typeCasting);
        }
        if ($result->autoloading !== null) {
            $this->renderAutoloading($result->autoloading);
        }
        if ($result->compression !== null) {
            $this->renderCompression($result->compression);
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
        echo '<p class="subtitle">' . $disk->cycles . ' cycles &middot; ms (lower is better)</p>';

        echo '<table><thead><tr>';
        foreach ($disk->fileCreationTimes as $blockSize => $_) {
            echo '<th>' . $this->esc(Size::bytesToFormat($blockSize)) . '</th>';
        }
        echo '</tr></thead><tbody><tr>';
        foreach ($disk->fileCreationTimes as $time) {
            echo '<td>' . \sprintf('%.2fms', $time * 1000) . '</td>';
        }
        echo '</tr></tbody></table>';

        echo '</div></section>';
    }

    private function renderMemory(MemoryResult $memory): void
    {
        echo '<section class="card card-wide">';
        echo '<div class="card-title">Memory</div>';
        echo '<div class="card-body">';
        echo '<p class="subtitle">' . $memory->cycles . ' cycles &middot; ms (lower is better)</p>';

        echo '<h3>Time</h3>';
        echo '<table><thead><tr>';
        foreach (\array_keys($memory->allocationTimes) as $name) {
            echo '<th>' . $this->esc($name) . '</th>';
        }
        echo '</tr></thead><tbody><tr>';
        foreach ($memory->allocationTimes as $time) {
            echo '<td>' . \sprintf('%.2fms', $time * 1000) . '</td>';
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

    private function renderSerialization(SerializationResult $serialization): void
    {
        echo '<section class="card card-wide">';
        echo '<div class="card-title">Serialization</div>';
        echo '<div class="card-body">';
        echo '<p class="subtitle">' . $serialization->cycles . ' cycles &middot; ms (lower is better)</p>';

        echo '<h3>Serialize</h3>';
        $this->renderResultTable($serialization->serializeTimes);

        echo '<h3>Unserialize</h3>';
        $this->renderResultTable($serialization->unserializeTimes);

        echo '</div></section>';
    }

    private function renderHashing(HashingResult $hashing): void
    {
        echo '<section class="card card-wide">';
        echo '<div class="card-title">Hashing</div>';
        echo '<div class="card-body">';

        echo '<h3>Hash Functions <small>(' . $hashing->hashCycles . ' cycles)</small></h3>';
        $this->renderResultTable($hashing->hashTimes);

        echo '<h3>Password Hashing <small>(' . $hashing->passwordCycles . ' cycles)</small></h3>';
        $this->renderResultTable($hashing->passwordTimes);

        echo '</div></section>';
    }

    private function renderEncryption(EncryptionResult $encryption): void
    {
        echo '<section class="card card-wide">';
        echo '<div class="card-title">Encryption</div>';
        echo '<div class="card-body">';
        echo '<p class="subtitle">' . $encryption->cycles . ' cycles &middot; ms (lower is better)</p>';

        echo '<h3>Encrypt</h3>';
        $this->renderResultTable($encryption->encryptTimes);

        echo '<h3>Decrypt</h3>';
        $this->renderResultTable($encryption->decryptTimes);

        echo '</div></section>';
    }

    private function renderFunctionOverhead(FunctionOverheadResult $functionOverhead): void
    {
        echo '<section class="card card-wide">';
        echo '<div class="card-title">Function Call Overhead</div>';
        echo '<div class="card-body">';
        echo '<p class="subtitle">' . $functionOverhead->cycles . ' cycles &middot; ms (lower is better)</p>';

        $this->renderResultTable($functionOverhead->callTimes);

        echo '</div></section>';
    }

    private function renderTypeCasting(TypeCastingResult $typeCasting): void
    {
        echo '<section class="card card-wide">';
        echo '<div class="card-title">Type Casting</div>';
        echo '<div class="card-body">';
        echo '<p class="subtitle">' . $typeCasting->cycles . ' cycles &middot; ms (lower is better)</p>';

        $this->renderResultTable($typeCasting->castTimes);

        echo '</div></section>';
    }

    private function renderAutoloading(AutoloadingResult $autoloading): void
    {
        echo '<section class="card card-wide">';
        echo '<div class="card-title">Object Instantiation</div>';
        echo '<div class="card-body">';
        echo '<p class="subtitle">' . $autoloading->cycles . ' cycles &middot; ms (lower is better)</p>';

        $this->renderResultTable($autoloading->instantiationTimes);

        echo '</div></section>';
    }

    private function renderCompression(CompressionResult $compression): void
    {
        echo '<section class="card card-wide">';
        echo '<div class="card-title">Compression</div>';
        echo '<div class="card-body">';
        echo '<p class="subtitle">' . $compression->cycles . ' cycles &middot; ms (lower is better)</p>';

        echo '<h3>Compress</h3>';
        $this->renderResultTable($compression->compressTimes);

        echo '<h3>Decompress</h3>';
        $this->renderResultTable($compression->decompressTimes);

        echo '<h3>Ratio</h3>';
        echo '<table><thead><tr>';
        foreach (\array_keys($compression->ratios) as $name) {
            echo '<th>' . $this->esc($name) . '</th>';
        }
        echo '</tr></thead><tbody><tr>';
        foreach ($compression->ratios as $ratio) {
            echo '<td>' . \sprintf('%.1f%%', $ratio * 100) . '</td>';
        }
        echo '</tr></tbody></table>';

        echo '</div></section>';
    }

    private function renderCpu(CpuResult $cpu): void
    {
        echo '<section class="card card-wide">';
        echo '<div class="card-title">CPU</div>';
        echo '<div class="card-body">';

        echo '<p class="subtitle">ms (lower is better)</p>';

        echo '<h3>Math <small>(' . $cpu->mathCount . ' cycles)</small></h3>';
        $this->renderResultTable($cpu->mathResults);

        echo '<h3>Strings <small>(' . $cpu->stringCount . ' cycles)</small></h3>';
        $this->renderResultTable($cpu->stringResults);

        echo '<dl class="kv">';
        $this->dt('Loops', \sprintf('%.2fms', $cpu->loopsResult * 1000) . ' (' . $cpu->loopsCount . ' cycles)');
        $this->dt('If/Else', \sprintf('%.2fms', $cpu->ifElseResult * 1000) . ' (' . $cpu->ifElseCount . ' cycles)');
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
        $this->dt('Query Time', \sprintf('%.2fms', $mysql->queryTime * 1000) . ' (' . $mysql->queryCount . ' cycles, lower is better)');
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

    private function renderNetwork(NetworkResult $network): void
    {
        echo '<section class="card card-wide">';
        echo '<div class="card-title">Network</div>';
        echo '<div class="card-body">';
        echo '<p class="subtitle">' . $network->cycles . ' cycles &middot; average ms per cycle (lower is better)</p>';

        echo '<h3>DNS Resolution</h3>';
        $this->renderResultTable($network->dnsResolutionTimes);

        echo '<h3>TCP Connection</h3>';
        $this->renderResultTable($network->connectionTimes);

        echo '</div></section>';
    }

    private function renderJson(JsonResult $json): void
    {
        echo '<section class="card card-wide">';
        echo '<div class="card-title">JSON</div>';
        echo '<div class="card-body">';
        echo '<p class="subtitle">' . $json->cycles . ' cycles &middot; ms (lower is better)</p>';

        echo '<h3>Encode</h3>';
        $this->renderResultTable($json->encodeTimes);

        echo '<h3>Decode</h3>';
        $this->renderResultTable($json->decodeTimes);

        echo '</div></section>';
    }

    private function renderRegex(RegexResult $regex): void
    {
        echo '<section class="card card-wide">';
        echo '<div class="card-title">Regex</div>';
        echo '<div class="card-body">';
        echo '<p class="subtitle">' . $regex->cycles . ' cycles &middot; ms (lower is better)</p>';

        echo '<h3>Match</h3>';
        $this->renderResultTable($regex->matchTimes);

        echo '<h3>Replace</h3>';
        $this->renderResultTable($regex->replaceTimes);

        echo '</div></section>';
    }

    private function renderFileRead(FileReadResult $fileRead): void
    {
        echo '<section class="card card-wide">';
        echo '<div class="card-title">File Read</div>';
        echo '<div class="card-body">';
        echo '<p class="subtitle">' . $fileRead->cycles . ' cycles &middot; ms (lower is better)</p>';

        $this->renderResultTable($fileRead->readTimes);

        echo '</div></section>';
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
            echo '<td>' . \sprintf('%.2fms', $time * 1000) . '</td>';
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
            font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #1a1f2e;
            color: #c8ccd4;
            line-height: 1.6;
            min-height: 100vh;
        }
        .banner {
            background: linear-gradient(160deg, #1e2536 0%, #1a1f2e 50%, #1c2233 100%);
            border-bottom: 1px solid rgba(56, 189, 248, 0.1);
            color: #e2e8f0;
            padding: 3rem 2rem 2.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .banner::before {
            content: "";
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(56, 189, 248, 0.5), transparent);
        }
        .banner h1 {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #f1f5f9;
        }
        .banner .meta {
            margin-top: 0.75rem;
            display: flex;
            justify-content: center;
            gap: 2rem;
            font-size: 0.875rem;
            color: #64748b;
        }
        .banner .duration {
            font-family: "JetBrains Mono", "SF Mono", "Cascadia Code", monospace;
            color: #38bdf8;
            font-weight: 500;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
            gap: 1rem;
            padding: 1.5rem;
            max-width: 1440px;
            margin: 0 auto;
        }
        .card {
            background: #232a3b;
            border: 1px solid #2e3648;
            border-radius: 10px;
            overflow: hidden;
            transition: border-color 0.2s ease;
        }
        .card:hover { border-color: #3a4358; }
        .card-wide { grid-column: 1 / -1; }
        .card-title {
            background: linear-gradient(180deg, #283044 0%, #232a3b 100%);
            border-bottom: 1px solid #2e3648;
            color: #38bdf8;
            padding: 0.7rem 1.25rem;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .card-body { padding: 1.25rem; }
        .card-body h3 {
            margin: 1.25rem 0 0.5rem;
            color: #94a3b8;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .card-body h3 small { color: #475569; font-weight: 400; text-transform: none; letter-spacing: 0; }
        .card-body h3:first-child { margin-top: 0; }
        dl.kv { display: grid; grid-template-columns: auto 1fr; gap: 0.4rem 1.25rem; }
        dl.kv dt { color: #64748b; font-size: 0.85rem; }
        dl.kv dd {
            font-family: "JetBrains Mono", "SF Mono", "Cascadia Code", monospace;
            font-size: 0.85rem;
            color: #e2e8f0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0.5rem 0 1rem;
            font-size: 0.8rem;
        }
        table th {
            background: #1e2536;
            padding: 0.55rem 0.75rem;
            text-align: left;
            font-weight: 600;
            color: #94a3b8;
            border-bottom: 1px solid #2e3648;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        table td {
            padding: 0.55rem 0.75rem;
            border-bottom: 1px solid #2a3140;
            font-family: "JetBrains Mono", "SF Mono", "Cascadia Code", monospace;
            color: #4ade80;
            font-size: 0.8rem;
        }
        table tr:last-child td { border-bottom: none; }
        table tr:hover td { background: rgba(56, 189, 248, 0.03); }
        .subtitle {
            color: #475569;
            font-size: 0.8rem;
            margin-bottom: 0.75rem;
        }
        details { margin-top: 1rem; }
        summary {
            cursor: pointer;
            color: #94a3b8;
            font-weight: 500;
            font-size: 0.85rem;
            padding: 0.3rem 0;
            transition: color 0.15s;
        }
        summary:hover { color: #e2e8f0; }
        .ext-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
            margin-top: 0.5rem;
        }
        .ext {
            background: #2a3140;
            border: 1px solid #353e50;
            padding: 0.15rem 0.5rem;
            border-radius: 5px;
            font-size: 0.75rem;
            font-family: "JetBrains Mono", "SF Mono", "Cascadia Code", monospace;
            color: #c8ccd4;
            transition: border-color 0.15s;
        }
        .ext:hover { border-color: #38bdf8; }
        .ext small { color: #475569; }
        footer {
            text-align: center;
            padding: 1.5rem 2rem;
            color: #475569;
            font-size: 0.8rem;
            display: flex;
            justify-content: center;
            gap: 2rem;
            border-top: 1px solid #2a3140;
            margin-top: 0.5rem;
        }
        footer strong { color: #94a3b8; }
        footer code {
            background: #2a3140;
            border: 1px solid #353e50;
            padding: 0.1rem 0.4rem;
            border-radius: 4px;
            font-family: "JetBrains Mono", "SF Mono", "Cascadia Code", monospace;
            color: #94a3b8;
        }
        @media (max-width: 600px) {
            .grid { grid-template-columns: 1fr; padding: 1rem; gap: 0.75rem; }
            .banner { padding: 2rem 1rem 1.5rem; }
            .banner h1 { font-size: 1.3rem; }
            footer { flex-direction: column; gap: 0.5rem; }
        }
        CSS;
    }
}
