<?php

declare(strict_types=1);

namespace setasign\PhpStubGenerator\Parser\BetterReflection;

use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Identifier\IdentifierType;
use Roave\BetterReflection\Reflection\Adapter\ReflectionClass;
use Roave\BetterReflection\SourceLocator\SourceStubber\ReflectionSourceStubber;
use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\MemoizingSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;
use setasign\PhpStubGenerator\Parser\ParserInterface;
use setasign\PhpStubGenerator\Reader\ReaderInterface;

class BetterReflectionParser implements ParserInterface
{
    private ?array $classes = null;
//    private ?array $functions = null;
//    private ?array $constants = null;

    /**
     * @var null|array
     */
    private ?array $aliases = null;

    /**
     * @param ReaderInterface[] $sources
     * @param ReaderInterface[] $resolvingSources These sources are used to resolve reflections but won't generate stubs
     */
    public function __construct(private readonly array $sources, private readonly array $resolvingSources = [])
    {
    }

    private function resolveReaders(array $readers): array
    {
        $files = \array_map(function (ReaderInterface $source) {
            return $source->getFiles();
        }, \array_values($readers));
        return \array_merge(...$files);
    }

    /**
     * @inheritDoc
     */
    public function parse(): void
    {
        $betterReflection = new BetterReflection();
        $astLocator = new AstLocatorWithNamespaceUses($betterReflection->phpParser());

        $mainSourceLocator = new FileListSourceLocator($this->resolveReaders($this->sources), $astLocator);
        $secondarySourceLocators = [];
        if (count($this->resolvingSources) > 0) {
            $secondarySourceLocators[] = new FileListSourceLocator(
                $this->resolveReaders($this->resolvingSources),
                $astLocator
            );
        }

        // php internals
        $secondarySourceLocators[] = new MemoizingSourceLocator(
            new PhpInternalSourceLocator($astLocator, new ReflectionSourceStubber())
        );

        $reflector = new PriorizingReflector(
            $mainSourceLocator,
            new AggregateSourceLocator($secondarySourceLocators)
        );

        $classes = [];
//        $functions = [];
//        $constants = [];
        $aliases = [];

        $classIdent = new IdentifierType(IdentifierType::IDENTIFIER_CLASS);
        foreach ($reflector->reflectAllClasses() as $class) {
            $className = $class->getName();
            $classes[$class->getNamespaceName()][$className] = new ReflectionClass($class);

            $aliases[self::TYPE_CLASS][$className] = $astLocator->getNamespaceUses($classIdent, $className);
        }

//        $functionIdent = new IdentifierType(IdentifierType::IDENTIFIER_FUNCTION);
//        foreach ($reflector->reflectAllFunctions() as $function) {
//            $functionName = $function->getName();
//            $functions[$function->getNamespaceName()][$functionName] = new ReflectionFunction($function);
//
//            $aliases[self::TYPE_FUNCTION][$functionName] = $astLocator->getNamespaceUses($functionIdent, $className);
//        }

//        foreach ($reflector->reflectAllConstants() as $constant) {
//            $constants[$constant->getNamespaceName()][$constant->getName()] = $constant;
//        }

        $this->classes = $classes;
//        $this->functions = $functions;
//        $this->constants = $constants;
        $this->aliases = $aliases;
    }

    /**
     * @inheritdoc
     */
    public function getClasses(): array
    {
        if ($this->classes === null) {
            throw new \BadMethodCallException('BetterReflectionParser::parse wasn\'t called yet!');
        }

        return $this->classes;
    }

//    /**
//     * @inheritdoc
//     */
//    public function getFunctions(): array
//    {
//        if ($this->functions === null) {
//            throw new \BadMethodCallException('BetterReflectionParser::parse wasn\'t called yet!');
//        }
//
//        return $this->functions;
//    }

//    /**
//     * @inheritDoc
//     */
//    public function getConstants(): array
//    {
//        if ($this->constants === null) {
//            throw new \BadMethodCallException('BetterReflectionParser::parse wasn\'t called yet!');
//        }
//
//        return $this->constants;
//    }

    /**
     * @inheritdoc
     */
    public function getAliases(string $classOrFunctionName, string $type): array
    {
        if ($this->aliases === null) {
            throw new \BadMethodCallException('BetterReflectionParser::parse wasn\'t called yet!');
        }

        if (!\array_key_exists($classOrFunctionName, $this->aliases[$type])) {
            throw new \InvalidArgumentException(\sprintf(
                'Unknown class or function "%s"!',
                $classOrFunctionName
            ));
        }

        return $this->aliases[$type][$classOrFunctionName];
    }
}
