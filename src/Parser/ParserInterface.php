<?php

declare(strict_types=1);

namespace setasign\PhpStubGenerator\Parser;

use ReflectionClass;

interface ParserInterface
{
    public const TYPE_CLASS = 'class';
    public const TYPE_FUNCTION = 'function';

    /**
     * @return void
     */
    public function parse(): void;

    /**
     * @return array<string, ReflectionClass[]> Returns an array like this: ['NamespaceName' => ReflectionClass[]]
     * @throws \BadMethodCallException If parse wasn't called yet.
     */
    public function getClasses(): array;

//    /**
//     * @return array<string, ReflectionFunction[]>
//     * @throws \BadMethodCallException If parse wasn't called yet.
//     */
//    public function getFunctions(): array;
//
//    /**
//     * @return array<string, ReflectionConstant[]>
//     * @throws \BadMethodCallException If parse wasn't called yet.
//     */
//    public function getConstants(): array;

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
}
