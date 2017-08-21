<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Parser\GoaopParserReflection;

use Go\ParserReflection\ReflectionFileNamespace;
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
     * @var null|array
     */
    private $namespaces;

    /**
     * @var null|array
     */
    private $aliases;

    /**
     * GoaopParserReflectionParser constructor.
     *
     * @param ReaderInterface[] $sources
     */
    public function __construct(array $sources)
    {
        $this->sources = $sources;
    }

    /**
     * @inheritdoc
     */
    public function parse(): void
    {
        $this->resolveNamespaces();

        $paths = array_map(function (array $classes) {
            return array_map(function (ReflectionClass $reflectionClass) {
                return $reflectionClass->getFileName();
            }, $classes);
        }, $this->namespaces);

        $classMap = array_merge(...array_values($paths));
        $locator = new ClassListLocator($classMap);
        ReflectionEngine::init($locator);
    }

    /**
     * @inheritdoc
     */
    public function getClasses(): array
    {
        if ($this->namespaces === null) {
            throw new \BadMethodCallException('GoaopParserReflectionParser::parse wasn\'t called yet!');
        }

        return $this->namespaces;
    }

    /**
     * @inheritdoc
     */
    public function getAliases(string $className): array
    {
        if ($this->aliases === null) {
            throw new \BadMethodCallException('GoaopParserReflectionParser::parse wasn\'t called yet!');
        }

        if (!array_key_exists($className, $this->aliases)) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown class "%s"!',
                $className
            ));
        }

        return $this->aliases[$className];
    }

    protected function resolveNamespaces(): void
    {
        $files = array_map(function (ReaderInterface $source) {
            return $source->getFiles();
        }, array_values($this->sources));
        $files = array_merge(...$files);

        $files = array_map(function (string $file) {
            return new ReflectionFile($file);
        }, $files);

        $fileNamespaces = array_map(function (ReflectionFile $file) {
            return $file->getFileNamespaces();
        }, $files);

        /**
         * @var array $namespaces
         */
        $namespaces = [];
        $aliases = [];
        array_walk($fileNamespaces, function (array $fileNamespaces) use (&$namespaces, &$aliases) {
            foreach ($fileNamespaces as $fileNamespace) {
                /**
                 * @var ReflectionFileNamespace $fileNamespace
                 */
                $namespace = $fileNamespace->getName();
                $namespaceAliases = $fileNamespace->getNamespaceAliases();
                foreach ($fileNamespace->getClasses() as $class) {
                    $className = $class->getName();
                    $namespaces[$namespace][$className] = $class;
                    $aliases[$className] = $namespaceAliases;
                }
            }
        });
        $this->namespaces = $namespaces;
        $this->aliases = $aliases;
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
