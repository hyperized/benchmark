# PHP benchmark

[![CI](https://github.com/hyperized/benchmark/actions/workflows/ci.yml/badge.svg)](https://github.com/hyperized/benchmark/actions/workflows/ci.yml)

Simple PHP server benchmarking utility that tests hosting environment capabilities:

- **PHP** — version, ini settings (memory, uploads, execution time), loaded extensions
- **CPU** — math functions, string operations, loops, conditionals
- **Disk I/O** — file creation across block sizes
- **MySQL** — query performance

Auto-detects CLI vs web and renders with the appropriate output mode:
- **CLI** — ANSI colors with Unicode box-drawing tables
- **Web** — styled HTML dashboard with responsive CSS grid layout

Each run saves a JSON report with a timestamp to `reports/`.

## Requirements

- PHP >= 8.4
- ext-mysqli (for MySQL benchmarks)

## Installation

```bash
composer create-project hyperized/benchmark:dev-master
cp config/config.yml.example config/config.yml
```

Adjust `config/config.yml` to your preferences. Set module toggles, cycle counts, MySQL credentials, and optionally override the output format.

## Usage

### CLI

```bash
php benchmark.php
```

### Development server

```bash
php -S localhost:8000 benchmark.php
```

### Remote

Install on the server via Composer and visit `/benchmark.php` in the browser.

### Reports

JSON reports are saved to `reports/` with timestamped filenames (e.g. `2026-04-11_143022.json`). Disable via `benchmark.output.report: false` in config.

### Security

If deploying to a web server, ensure `config.yml` is not publicly accessible. For Apache with `mod_rewrite`:

```apache
<Files "config.yml">
    deny from all
</Files>
```

## Development

```bash
composer analyse    # Run PHPStan (level 8)
composer cs-fix     # Auto-fix code style (PER-CS2.0)
composer cs-check   # Check code style (dry run)
```

## Contribution

I'm open to improvements and new benchmarks via [pull requests](https://github.com/hyperized/benchmark/pulls).

Issues can be reported through [Issues](https://github.com/hyperized/benchmark/issues).
Please include the full output of the script and your config file without the password.

## License

MIT
