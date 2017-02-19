Apache Tika Client for PHP
==========================

[![Build Status](https://travis-ci.org/polster/php-tika-client.svg?branch=master)](https://travis-ci.org/polster/php-tika-client)
[![Code Climate](https://lima.codeclimate.com/github/polster/php-tika-client/badges/gpa.svg)](https://lima.codeclimate.com/github/polster/php-tika-client)
[![Test Coverage](https://lima.codeclimate.com/github/polster/php-tika-client/badges/coverage.svg)](https://lima.codeclimate.com/github/polster/php-tika-client/coverage)
[![Issue Count](https://lima.codeclimate.com/github/polster/php-tika-client/badges/issue_count.svg)](https://lima.codeclimate.com/github/polster/php-tika-client)

## Purpose

* Light weight Apache Tika client written in PHP
* Supports the integration of the Tika server instead of having to call the Tika app JAR directly also instantiating the JVM each time (performance)

## Prerequisites

* PHP 5.6 or 7
* [composer](https://getcomposer.org/)

## User Manual

### Integration

* Add the following lines to your project's composer.json and adjust the version as needed:
```
"repositories": [
  {
    "type": "git",
    "url": "https://github.com/polster/php-tika-client"
  }
],
"require": {
  "php": "5.6.*",
  "polster/php-tika-client": "1.1.0"
}
```

### API Documentation

* Git clone this project and cd into the same
* Install the required dependencies:
```
composer install
```
* Run the following command in order to generate the API documentation:
```
vendor/bin/phpdoc -d ./src -t ./docs/api
```
* Open the following file with your favorite web browser:
```
docs/api/index.html
```