<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Parser\GoaopParserReflection;

use ReflectionClass;
use Go\ParserReflection\ReflectionEngine;
use Go\ParserReflection\ReflectionFile;
use setasign\PhpStubGenerator\Reader\ReaderInterface;
use setasign\PhpStubGenerator\Parser\ParserInterface;
use setasign\PhpStubGenerator\Parser\ReflectionConst;
use setasign\PhpStubGenerator\Parser\GoaopParserReflection\ReflectionConst as GoaopReflectionConst;

class GoaopParserReflectionParser implements ParserInterface
{
    /**
     * @var ReaderInterface[]
     */
    private $sources;

    /**
     * GoaopParserReflectionParser constructor.
     *
     * @param ReaderInterface[] $sources
     */
    public function __construct(array $sources)
    {
        $this->sources = $sources;
    }

    public function parse(): array
    {
        $namespaces = $this->resolveNamespaces();

        $paths = array_map(function (array $classes) {
            return array_map(function (ReflectionClass $reflectionClass) {
                return $reflectionClass->getFileName();
            }, $classes);
        }, $namespaces);

        $classMap = array_merge(...array_values($paths));
        $locator = new ClassListLocator($classMap);
        ReflectionEngine::init($locator);

        return $namespaces;
    }

    /**
     * @return ReflectionClass[][]
     */
    protected function resolveNamespaces(): array
    {
        $files = array_map(function (ReaderInterface $source) {
            return array_map(function (string $file) {
                return new ReflectionFile($file);
            }, $source->getFiles());
        }, $this->sources);
        $files = array_merge(...array_values($files));

        $fileNamespaces = array_map(function (ReflectionFile $file) {
            return $file->getFileNamespaces();
        }, $files);

        /**
         * @var array $namespaces
         */
        $namespaces = array_reduce($fileNamespaces, function (array $carry, array $fileNamespaces) {
            foreach ($fileNamespaces as $fileNamespace) {
                $carry[$fileNamespace->getName()][] = $fileNamespace->getClasses();
            }
            return $carry;
        }, []);

        foreach ($namespaces as $name => $classes) {
            $namespaces[$name] = array_merge(...$classes);
        }

        return $namespaces;
    }

    public function getConstantReflection(\ReflectionClass $reflectionClass, string $constantName): ?ReflectionConst
    {
        $reflectionConst = null;
        try {
            $reflectionConst = new GoaopReflectionConst($reflectionClass, $constantName);
        } catch (\ReflectionException $e) {
        }

        return $reflectionConst;
    }
}
