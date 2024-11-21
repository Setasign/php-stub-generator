<?php

declare(strict_types=1);

namespace setasign\PhpStubGenerator\Reader;

class SingleFile implements ReaderInterface
{
    public function __construct(protected string $filename)
    {
        if (!\is_file($this->filename)) {
            throw new \InvalidArgumentException('File "' . $this->filename . '" does not exist.');
        }
    }

    /**
     * @inheritDoc
     */
    public function getFiles(): array
    {
        return [$this->filename];
    }
}
