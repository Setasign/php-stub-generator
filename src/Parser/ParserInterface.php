<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Parser;

interface ParserInterface
{
    /**
     * @return void
     */
    public function parse(): void;

    /**
     * @return array Returns an array like this: ['NamespaceName' => ReflectionClass[]]
     * @throws \BadMethodCallException If parse wasn't called yet.
     */
    public function getClasses(): array;

    /**
     * Returns an array with all use aliases for class $className.
     *
     * @param string $className
     * @return array
     * @throws \BadMethodCallException If parse wasn't called yet.
     * @throws \InvalidArgumentException If the class can't be found.
     */
    public function getAliases(string $className): array;

    /**
     * @param \ReflectionClass $reflectionClass
     * @param string $constantName
     * @return null|ReflectionConst
     */
    public function getConstantReflection(\ReflectionClass $reflectionClass, string $constantName): ?ReflectionConst;
}
