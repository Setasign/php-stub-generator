<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Reader;

interface ReaderInterface
{
    /**
     * @return string[]
     */
    public function getFiles(): array;
}
