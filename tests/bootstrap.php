<?php
ini_set('display_errors', 1);

if (defined("AUTOLOAD_PATH")) {
    if (is_file(__DIR__ . '/../' . AUTOLOAD_PATH)) {
        include_once __DIR__ . '/../' . AUTOLOAD_PATH;
    } else {
        throw new InvalidArgumentException(sprintf(
            'Can\'t load custom autoload file located at %s',
            AUTOLOAD_PATH
        ));
    }
}
