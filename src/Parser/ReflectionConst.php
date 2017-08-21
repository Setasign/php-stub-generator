<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Parser;

interface ReflectionConst
{
    /**
     * @return null|string
     */
    public function getDocComment(): ?string;
}
