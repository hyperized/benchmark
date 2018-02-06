# PHP benchmark
[![Build Status](https://scrutinizer-ci.com/g/hyperized/benchmark/badges/build.png?b=master)](https://scrutinizer-ci.com/g/hyperized/benchmark/build-status/master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/hyperized/benchmark/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/hyperized/benchmark/?branch=master)

Simple PHP server benchmarking.

This tool can help you determine if a hosting environment is suited for your projects in terms of:
- PHP.ini settings that affect uploads;
- CPU speed available to your PHP instance;
- Disk IOPS available to your PHP instance;
- MySQL query speed;

## How to install:
    composer create-project hyperized/benchmark:dev-master
    
Copy the `/config/config.yml.example` to `/config/config.yml` and adjust to your preferences.
    
## How to run:

### Locally with CLI
    php benchmark.php

### Locally with development server

    php -S localhost:8000 benchmark.php
    
### Remotely
Install on the server by running composer and visiting the `/benchmark.php` page of the directory the project is installed at.

### Security
Note that you might want to add additional security to your server to not expose the config.yml file to your webtraffic.

For Apache with `mod_rewrite` you can use something like this in your `.htaccess` file:

    <Files "config.yml">
        deny from all
    </Files>

## Contribution
I'm open to improvements and new benchmarks via [pull requests](https://github.com/hyperized/benchmark/pulls)

Issues can be reported through [Issues](https://github.com/hyperized/benchmark/issues).
Please include the full output of the script and your config file without the password.

## Credit
Credit where credit is due:

- https://github.com/odan/benchmark-php
- https://gist.github.com/RamadhanAmizudin/ca87f7be83c6237bb070
- https://stackoverflow.com/a/25370978/1757763
- http://php.net/manual/en/function.rmdir.php#119949
