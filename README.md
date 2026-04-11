# PHP benchmark

[![CI](https://github.com/hyperized/benchmark/actions/workflows/ci.yml/badge.svg)](https://github.com/hyperized/benchmark/actions/workflows/ci.yml)

Comprehensive PHP server benchmarking utility. Tests your hosting environment across 15 benchmark modules and reports results in milliseconds.

Auto-detects CLI vs web and renders with the appropriate output mode:
- **CLI** — ANSI colors with Unicode box-drawing tables
- **Web** — styled HTML dashboard with responsive CSS grid layout

Each run saves a JSON report with a timestamp to `reports/`.

## Benchmark Modules

| Category | Module | What it measures |
|----------|--------|-----------------|
| **Environment** | PHP | Version, ini settings, memory limits, loaded extensions |
| **I/O** | Disk I/O | File creation across 8 block sizes (512B–64KB) |
| | File Read | Sequential reads across file sizes (1KB–4MB) |
| | Network | DNS resolution and TCP connection latency |
| **Compute** | CPU | Math functions, string operations, loops, conditionals |
| | Memory | Allocation speed and peak memory per operation |
| | Regex | `preg_match` and `preg_replace` across 10 pattern types |
| | JSON | Encode/decode with small, medium, and large payloads |
| | Serialization | `serialize` vs `json_encode` vs `var_export` (+ igbinary/msgpack if available) |
| | Hashing | md5, sha1, sha256, sha512, xxh3, crc32, hmac-sha256, bcrypt, argon2id |
| | Encryption | AES-128/256-CBC/GCM, ChaCha20-Poly1305 encrypt and decrypt |
| | Compression | gzip, zlib, deflate (+ brotli/lzf if available) with compression ratios |
| **Runtime** | Function Overhead | Named function, closure, first-class callable, static/instance method, arrow function |
| | Type Casting | int/string/float/bool conversions |
| | Object Instantiation | stdClass, DateTime, SplStack, SplPriorityQueue, ArrayObject |
| **Database** | MySQL | Query performance (requires ext-mysqli) |

All modules can be individually enabled/disabled and configured in `config/config.yml`.

## Requirements

- PHP >= 8.4
- ext-mysqli (for MySQL benchmarks, optional)

## Installation

```bash
composer create-project hyperized/benchmark
cp config/config.yml.example config/config.yml
```

Adjust `config/config.yml` to your preferences. Set module toggles, cycle counts, MySQL credentials, and optionally override the output format.

## Usage

### CLI

```bash
php benchmark.php
```

Example output:

```
╔════════════════════════════════════════════════╗
║  PHP Benchmark Report                          ║
║  2026-04-11 14:30:22                           ║
╚════════════════════════════════════════════════╝

── PHP ───────────────────────────────────────────

  PHP Version     8.5.4
  Server          CLI
  Max Memory      128.00 MB
  Max Upload      2.00 MB
  Max Exec Time   0 seconds
  Extensions      67 loaded

── Disk I/O ──────────────────────────────────────

  Cycles: 100
  ms (lower is better)

  ┌──────────┬──────────┬──────────┬──────────┐
  │ 512.00 B │ 1.00 KB  │ 2.00 KB  │ 4.00 KB  │ ...
  ├──────────┼──────────┼──────────┼──────────┤
  │ 9.55ms   │ 9.78ms   │ 9.46ms   │ 10.97ms  │ ...
  └──────────┴──────────┴──────────┴──────────┘

── Hashing ───────────────────────────────────────

  Hash Functions (99999 cycles, ms, lower is better)

  ┌──────────┬──────────┬──────────┬──────────┬──────────┐
  │ md5      │ sha1     │ sha256   │ sha512   │ xxh3     │ ...
  ├──────────┼──────────┼──────────┼──────────┼──────────┤
  │ 143.00ms │ 155.71ms │ 387.45ms │ 288.95ms │ 16.35ms  │ ...
  └──────────┴──────────┴──────────┴──────────┴──────────┘

  ...

──────────────────────────────────────────────────
  Total Duration  18.657s
  Report saved    reports/2026-04-11_143022.json
──────────────────────────────────────────────────
```

### Web

```bash
php -S localhost:8000 benchmark.php
```

Open `http://localhost:8000` for a styled HTML dashboard with cards for each module.

### Remote

Install on the server via Composer and visit `/benchmark.php` in the browser.

### Reports

JSON reports are saved to `reports/` with timestamped filenames (e.g. `2026-04-11_143022.json`). Disable via `benchmark.output.report: false` in config.

### Configuration

All modules support `enabled: true/false` and configurable cycle counts:

```yaml
benchmark:
  output:
    format: ~        # null = auto-detect, 'cli', or 'html'
    report: true     # save JSON report to reports/
  php:
    enabled: true
  disk:
    enabled: true
    cycles: 100
  memory:
    enabled: true
    count: 9999
  network:
    enabled: true
    cycles: 3
    hosts:
      - google.com
      - cloudflare.com
      - github.com
  hashing:
    enabled: true
    hashCount: 99999
    passwordCount: 10
  # ... see config/config.yml.example for all options
```

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
