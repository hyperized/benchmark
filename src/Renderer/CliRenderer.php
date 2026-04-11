<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Renderer;

use Hyperized\Benchmark\Generic\Size;
use Hyperized\Benchmark\Result\BenchmarkResult;
use Hyperized\Benchmark\Result\CpuResult;
use Hyperized\Benchmark\Result\DiskResult;
use Hyperized\Benchmark\Result\MySqlResult;
use Hyperized\Benchmark\Result\PhpResult;

final class CliRenderer implements RendererInterface
{
    private const string RESET = "\033[0m";
    private const string BOLD = "\033[1m";
    private const string DIM = "\033[2m";
    private const string GREEN = "\033[32m";
    private const string YELLOW = "\033[33m";
    private const string BOLD_CYAN = "\033[1;36m";
    private const string BOLD_WHITE = "\033[1;37m";

    private const int WIDTH = 50;

    public function render(BenchmarkResult $result, ?string $reportPath): void
    {
        $this->renderHeader($result);

        if ($result->php !== null) {
            $this->renderPhp($result->php);
        }
        if ($result->disk !== null) {
            $this->renderDisk($result->disk);
        }
        if ($result->cpu !== null) {
            $this->renderCpu($result->cpu);
        }
        if ($result->mysql !== null) {
            $this->renderMySql($result->mysql);
        }

        $this->renderFooter($result, $reportPath);
    }

    private function renderHeader(BenchmarkResult $result): void
    {
        $title = 'PHP Benchmark Report';
        $date = $result->timestamp->format('Y-m-d H:i:s');
        $innerWidth = self::WIDTH - 2;

        echo "\n";
        echo self::BOLD_CYAN . '╔' . \str_repeat('═', $innerWidth) . '╗' . self::RESET . "\n";
        echo self::BOLD_CYAN . '║' . self::BOLD_WHITE . \str_pad('  ' . $title, $innerWidth) . self::BOLD_CYAN . '║' . self::RESET . "\n";
        echo self::BOLD_CYAN . '║' . self::DIM . \str_pad('  ' . $date, $innerWidth) . self::BOLD_CYAN . '║' . self::RESET . "\n";
        echo self::BOLD_CYAN . '╚' . \str_repeat('═', $innerWidth) . '╝' . self::RESET . "\n";
    }

    private function renderPhp(PhpResult $php): void
    {
        $this->sectionTitle('PHP');

        $this->keyValue('PHP Version', $php->phpVersion);
        $this->keyValue('Server', $php->server);
        $this->keyValue('Max Memory', Size::bytesToFormat($php->maxMemoryBytes));
        $this->keyValue('Max Upload', Size::bytesToFormat($php->maxUploadBytes));
        $this->keyValue('Max Exec Time', $php->maxExecutionTime . ' seconds');
        $this->keyValue('Extensions', \count($php->extensions) . ' loaded');
        echo "\n";
    }

    private function renderDisk(DiskResult $disk): void
    {
        $this->sectionTitle('Disk I/O');

        echo '  ' . self::DIM . 'Cycles: ' . self::RESET . self::GREEN . $disk->cycles . self::RESET . "\n";
        echo '  ' . self::DIM . 'Time in seconds (lower is better)' . self::RESET . "\n\n";

        /** @var array<string, string> $headers */
        $headers = [];
        /** @var array<string, string> $values */
        $values = [];
        foreach ($disk->fileCreationTimes as $blockSize => $time) {
            $label = Size::bytesToFormat($blockSize);
            $headers[$label] = $label;
            $values[$label] = \sprintf('%.4fs', $time);
        }

        echo $this->buildTable($headers, [$values]);
    }

    private function renderCpu(CpuResult $cpu): void
    {
        $this->sectionTitle('CPU');

        echo '  ' . self::DIM . 'Time in seconds (lower is better)' . self::RESET . "\n\n";

        echo '  ' . self::BOLD . 'Math' . self::RESET . self::DIM . ' (' . $cpu->mathCount . ' cycles)' . self::RESET . "\n\n";
        echo $this->buildResultTable($cpu->mathResults);

        echo '  ' . self::BOLD . 'Strings' . self::RESET . self::DIM . ' (' . $cpu->stringCount . ' cycles)' . self::RESET . "\n\n";
        echo $this->buildResultTable($cpu->stringResults);

        $this->keyValue('Loops', \sprintf('%.6fs', $cpu->loopsResult) . self::DIM . ' (' . $cpu->loopsCount . ' cycles)');
        $this->keyValue('If/Else', \sprintf('%.6fs', $cpu->ifElseResult) . self::DIM . ' (' . $cpu->ifElseCount . ' cycles)');
        echo "\n";
    }

    private function renderMySql(MySqlResult $mysql): void
    {
        $this->sectionTitle('MySQL');

        $this->keyValue('Version', (string) $mysql->version);
        $this->keyValue('Query Time', \sprintf('%.4fs', $mysql->queryTime) . self::DIM . ' (' . $mysql->queryCount . ' cycles, lower is better)');
        echo "\n";
    }

    private function renderFooter(BenchmarkResult $result, ?string $reportPath): void
    {
        echo self::DIM . \str_repeat('─', self::WIDTH) . self::RESET . "\n";
        echo '  ' . self::DIM . 'Total Duration  ' . self::RESET . self::GREEN . \sprintf('%.3fs', $result->totalDuration) . self::RESET . "\n";
        if ($reportPath !== null) {
            echo '  ' . self::DIM . 'Report saved    ' . self::RESET . self::YELLOW . $reportPath . self::RESET . "\n";
        }
        echo self::DIM . \str_repeat('─', self::WIDTH) . self::RESET . "\n\n";
    }

    private function sectionTitle(string $title): void
    {
        $line = \str_repeat('─', self::WIDTH - \mb_strlen($title) - 4);
        echo "\n" . self::BOLD_CYAN . '── ' . $title . ' ' . $line . self::RESET . "\n\n";
    }

    private function keyValue(string $key, string $value): void
    {
        echo '  ' . self::DIM . \str_pad($key, 16) . self::RESET . self::GREEN . $value . self::RESET . "\n";
    }

    /**
     * @param array<string, float> $results
     */
    private function buildResultTable(array $results): string
    {
        /** @var array<string, string> $headers */
        $headers = [];
        /** @var array<string, string> $values */
        $values = [];
        foreach ($results as $name => $time) {
            $headers[$name] = $name;
            $values[$name] = \sprintf('%.4fs', $time);
        }

        return $this->buildTable($headers, [$values]);
    }

    /**
     * @param array<string, string> $headers
     * @param list<array<string, string>> $rows
     */
    private function buildTable(array $headers, array $rows): string
    {
        // Calculate column widths
        /** @var array<string, int> $widths */
        $widths = [];
        foreach ($headers as $key => $header) {
            $widths[$key] = \max(\mb_strlen($header), 8);
            foreach ($rows as $row) {
                $widths[$key] = \max($widths[$key], \mb_strlen($row[$key] ?? ''));
            }
        }

        $output = '';
        $pad = 1;

        // Top border
        $output .= '  ' . self::DIM . '┌';
        $first = true;
        foreach ($widths as $w) {
            if (!$first) {
                $output .= '┬';
            }
            $output .= \str_repeat('─', $w + $pad * 2);
            $first = false;
        }
        $output .= '┐' . self::RESET . "\n";

        // Header row
        $output .= '  ' . self::DIM . '│' . self::RESET;
        foreach ($headers as $key => $header) {
            $output .= \str_repeat(' ', $pad) . self::BOLD . \str_pad($header, $widths[$key]) . self::RESET . \str_repeat(' ', $pad) . self::DIM . '│' . self::RESET;
        }
        $output .= "\n";

        // Separator
        $output .= '  ' . self::DIM . '├';
        $first = true;
        foreach ($widths as $w) {
            if (!$first) {
                $output .= '┼';
            }
            $output .= \str_repeat('─', $w + $pad * 2);
            $first = false;
        }
        $output .= '┤' . self::RESET . "\n";

        // Data rows
        foreach ($rows as $row) {
            $output .= '  ' . self::DIM . '│' . self::RESET;
            foreach ($headers as $key => $_) {
                $output .= \str_repeat(' ', $pad) . self::GREEN . \str_pad($row[$key] ?? '', $widths[$key]) . self::RESET . \str_repeat(' ', $pad) . self::DIM . '│' . self::RESET;
            }
            $output .= "\n";
        }

        // Bottom border
        $output .= '  ' . self::DIM . '└';
        $first = true;
        foreach ($widths as $w) {
            if (!$first) {
                $output .= '┴';
            }
            $output .= \str_repeat('─', $w + $pad * 2);
            $first = false;
        }
        $output .= '┘' . self::RESET . "\n\n";

        return $output;
    }
}
