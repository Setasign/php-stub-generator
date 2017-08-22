# php-stub-generator
A tool to generate stub-files for your php classes.

The main purpose for this tool is to generate stub-files from php classes to have code 
completion for your IDE when encrypting your library with e.g. 
[the ioncube encoder](http://www.ioncube.com/php_encoder.php).

[![Build Status](https://travis-ci.org/Setasign/php-stub-generator.svg?branch=master)](https://travis-ci.org/Setasign/php-stub-generator)
[![Total Downloads](https://poser.pugx.org/setasign/php-stub-generator/downloads.svg)](https://packagist.org/packages/setasign/php-stub-generator)
[![Minimum PHP Version](http://img.shields.io/badge/php-%3E%3D%207.1-8892BF.svg)](https://php.net/)
[![License](https://img.shields.io/packagist/l/setasign/php-stub-generator.svg)](https://packagist.org/packages/setasign/php-stub-generator)

## Installation

A basic installation via Composer could be done this way:

```bash
$ composer require setasign/php-stub-generator
```

Composer will install the library to your project's `vendor/setasign/php-stub-generator` directory.


## Basic usage

```php
<?php

use setasign\PhpStubGenerator\PhpStubGenerator;
use setasign\PhpStubGenerator\Reader\AllFiles;

require_once __DIR__ . '/vendor/autoload.php';

$generator = new PhpStubGenerator();
$generator->addSource(
    'setapdf-core',
    new AllFiles(__DIR__ . '/vendor/setasign/setapdf-core/library')
);
$output = $generator->generate();

file_put_contents(__DIR__ . '/setapdf-core-stub.php', $output);
```

## TODO
- Traits are not supported yet
- Command line tool: bin/generate-stub vendor/setasign/setapdf-core/library setapdf-core-stub.php
