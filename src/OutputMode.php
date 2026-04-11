<?php

declare(strict_types=1);

namespace Hyperized\Benchmark;

enum OutputMode: string
{
    case Cli = 'cli';
    case Html = 'html';
}
