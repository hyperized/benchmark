<?php

declare(strict_types=1);

namespace Hyperized\Benchmark\Report;

final readonly class ReportWriter
{
    public function __construct(
        private string $reportsDirectory,
    ) {}

    public function write(Report $report): string
    {
        if (!\is_dir($this->reportsDirectory)) {
            \mkdir($this->reportsDirectory, 0755, true);
        }

        $filename = $report->result->timestamp->format('Y-m-d_His') . '.json';
        $filePath = $this->reportsDirectory . DIRECTORY_SEPARATOR . $filename;

        \file_put_contents(
            $filePath,
            \json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
        );

        return $filePath;
    }
}
