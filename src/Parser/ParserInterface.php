<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Parser;

interface ParserInterface
{
    /**
     * @return array
     */
    public function parse(): array;

    /**
     * @param \ReflectionClass $reflectionClass
     * @param string $constantName
     * @return null|ReflectionConst
     */
    public function getConstantReflection(\ReflectionClass $reflectionClass, string $constantName): ?ReflectionConst;
}
