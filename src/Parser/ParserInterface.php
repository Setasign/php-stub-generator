<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Parser;

interface ParserInterface
{
    public const TYPE_CLASS = 'class';
    public const TYPE_FUNCTION = 'function';

    /**
     * @return void
     */
    public function parse(): void;

    /**
     * @return array Returns an array like this: ['NamespaceName' => \ReflectionClass[]]
     * @throws \BadMethodCallException If parse wasn't called yet.
     */
    public function getClasses(): array;

    /**
     * @return array Returns an array like this: ['NamespaceName' => \ReflectionFunction[]]
     * @throws \BadMethodCallException If parse wasn't called yet.
     */
    public function getFunctions(): array;

    /**
     * @return array Returns an array like this: ['NamespaceName' => ['ConstantName' => 'ConstantValue']]
     * @throws \BadMethodCallException If parse wasn't called yet.
     */
    public function getConstants(): array;

    /**
     * Returns an array with all use aliases for $classOrFunctionName.
     *
     * @param string $classOrFunctionName
     * @param string $type See self::TYPE_*
     * @return array
     * @throws \BadMethodCallException If parse wasn't called yet.
     * @throws \InvalidArgumentException If the class or function is unknown.
     */
    public function getAliases(string $classOrFunctionName, string $type): array;

    /**
     * @param \ReflectionClass $reflectionClass
     * @param string $constantName
     * @return null|ReflectionConst
     */
    public function getConstantReflection(\ReflectionClass $reflectionClass, string $constantName): ?ReflectionConst;
}
