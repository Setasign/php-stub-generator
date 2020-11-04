# php-stub-generator
A tool to generate stub-files for your php classes.

The main purpose for this tool is to generate stub-files from php classes to have code 
completion for your IDE when encrypting your library with e.g. 
[the ioncube encoder](http://www.ioncube.com/php_encoder.php).

[![Minimum PHP Version](http://img.shields.io/badge/php-%3E%3D%207.2-8892BF.svg)](https://php.net/)
[![License](https://img.shields.io/packagist/l/setasign/php-stub-generator.svg)](https://packagist.org/packages/setasign/php-stub-generator)

## Installation

A basic installation via Composer could be done this way:

```bash
composer require setasign/php-stub-generator
```

Composer will install the tool to your project's `vendor/setasign/php-stub-generator` directory.


## Basic usage

```php
<?php
declare(strict_types=1);

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

Alternatively you could just call the cli helper.

```bash
vendor/bin/php-stub-generator generate setapdf-core/library setapdf-core-stub.php
```

## Settings
The PhpStubGenerator class has following settings:
```php
class PhpStubGenerator
{
    /**
     * End of line character(s).
     *
     * Doesn't change the used EOL character(s) of doc blocks.
     *
     * @var string
     */
    public static $eol = "\n";

    /**
     * Tab character(s)
     *
     * @var string
     */
    public static $tab = '    ';

    /**
     * If enabled all generated class constants get a visibility (the generated stubs require PHP >= 7.1)
     * 
     * Within the cli tool can be set with the option "--addClassConstantsVisibility"
     * 
     * @var bool
     */
    public static $addClassConstantsVisibility = false;
}
```

## Drawbacks / TODOs
- Traits are not supported yet and probably won't be because of bugs like [this](https://bugs.php.net/bug.php?id=69180).
  The actual reflection api doesn't give enough information to rebuild the conflict resolution block.
  Additionally the "declaring class" of imported trait methods is the importing class and not like expected the trait.
- Calculated constants or constants that use other constants like \_\_DIR\_\_ will be filled with the values of the 
  runtime environment.
