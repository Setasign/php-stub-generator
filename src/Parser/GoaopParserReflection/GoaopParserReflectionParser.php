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
    private $classes;

    /**
     * @var null|array
     */
    private $functions;

    /**
     * @var null|array
     */
    private $constants;

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

        $paths = \array_map(function (array $classes) {
            return \array_map(function (ReflectionClass $reflectionClass) {
                return $reflectionClass->getFileName();
            }, $classes);
        }, $this->classes);

        $classMap = \array_merge(...\array_values($paths));
        $locator = new ClassListLocator($classMap);
        ReflectionEngine::init($locator);
    }

    /**
     * @inheritdoc
     */
    public function getClasses(): array
    {
        if ($this->classes === null) {
            throw new \BadMethodCallException('GoaopParserReflectionParser::parse wasn\'t called yet!');
        }

        return $this->classes;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions(): array
    {
        if ($this->functions === null) {
            throw new \BadMethodCallException('GoaopParserReflectionParser::parse wasn\'t called yet!');
        }

        return $this->functions;
    }

    /**
     * @inheritDoc
     */
    public function getConstants(): array
    {
        if ($this->constants === null) {
            throw new \BadMethodCallException('GoaopParserReflectionParser::parse wasn\'t called yet!');
        }

        return $this->constants;
    }

    /**
     * @inheritdoc
     */
    public function getAliases(string $classOrFunctionName, string $type): array
    {
        if ($this->aliases === null) {
            throw new \BadMethodCallException('GoaopParserReflectionParser::parse wasn\'t called yet!');
        }

        if (!\array_key_exists($classOrFunctionName, $this->aliases[$type])) {
            throw new \InvalidArgumentException(\sprintf(
                'Unknown class or function "%s"!',
                $classOrFunctionName
            ));
        }

        return $this->aliases[$type][$classOrFunctionName];
    }

    protected function resolveNamespaces(): void
    {
        $files = \array_map(function (ReaderInterface $source) {
            return $source->getFiles();
        }, \array_values($this->sources));
        $files = \array_merge(...$files);

        $files = \array_map(function (string $file) {
            return new ReflectionFile($file);
        }, $files);

        $allFileNamespaces = \array_map(function (ReflectionFile $file) {
            return $file->getFileNamespaces();
        }, $files);

        /**
         * @var array $classes
         */
        $classes = [];
        $functions = [];
        $constants = [];
        $aliases = [];
        foreach ($allFileNamespaces as $fileNamespaces) {
            foreach ($fileNamespaces as $fileNamespace) {
                /**
                 * @var ReflectionFileNamespace $fileNamespace
                 */
                $namespace = $fileNamespace->getName();
                $namespaceAliases = $fileNamespace->getNamespaceAliases();
                foreach ($fileNamespace->getClasses() as $class) {
                    $className = $class->getName();
                    $classes[$namespace][$className] = $class;
                    $aliases[self::TYPE_CLASS][$className] = $namespaceAliases;
                }

                foreach ($fileNamespace->getFunctions() as $function) {
                    $functionName = $function->getName();
                    $functions[$namespace][$functionName] = $function;
                    $aliases[self::TYPE_FUNCTION][$functionName] = $namespaceAliases;
                }

                foreach ($fileNamespace->getConstants(true) as $constantName => $constantValue) {
                    $constants[$namespace][$constantName] = $constantValue;
                }
            }
        }
        $this->classes = $classes;
        $this->functions = $functions;
        $this->constants = $constants;
        $this->aliases = $aliases;
    }

    /**
     * @inheritdoc
     */
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
