<?php

declare(strict_types=1);

namespace setasign\PhpStubGenerator\Parser\BetterReflection;

use Roave\BetterReflection\Reflector\Reflector;
use Roave\BetterReflection\Identifier\Identifier;
use Roave\BetterReflection\Identifier\IdentifierType;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionConstant;
use Roave\BetterReflection\Reflection\ReflectionFunction;
use Roave\BetterReflection\Reflector\Exception\IdentifierNotFound;
use Roave\BetterReflection\SourceLocator\Type\SourceLocator;

use function assert;

/**
 * This reflector will priorize all classes, functions and constants from the $mainSourceLocator and the reflectAll*
 * methods only return the classes, functions and contants from the $mainSourceLocator.
 */
final class PriorizingReflector implements Reflector
{
    public function __construct(private SourceLocator $mainSourceLocator, private SourceLocator $secondarySourceLocator)
    {
    }

    /**
     * Create a ReflectionClass for the specified $className.
     *
     * @throws IdentifierNotFound
     */
    public function reflectClass(string $identifierName): ReflectionClass
    {
        $identifier = new Identifier($identifierName, new IdentifierType(IdentifierType::IDENTIFIER_CLASS));

        $classInfo = $this->mainSourceLocator->locateIdentifier($this, $identifier);

        if ($classInfo === null) {
            $classInfo = $this->secondarySourceLocator->locateIdentifier($this, $identifier);
        }

        if ($classInfo === null) {
            throw IdentifierNotFound::fromIdentifier($identifier);
        }

        assert($classInfo instanceof ReflectionClass);

        return $classInfo;
    }

    /**
     * Get all the classes available in the scope specified by the MainSourceLocator.
     *
     * @return list<ReflectionClass>
     */
    public function reflectAllClasses(): iterable
    {
        /** @var list<ReflectionClass> $allClasses */
        $allClasses = $this->mainSourceLocator->locateIdentifiersByType(
            $this,
            new IdentifierType(IdentifierType::IDENTIFIER_CLASS),
        );

        return $allClasses;
    }

    /**
     * Create a ReflectionFunction for the specified $functionName.
     *
     * @throws IdentifierNotFound
     */
    public function reflectFunction(string $identifierName): ReflectionFunction
    {
        $identifier = new Identifier($identifierName, new IdentifierType(IdentifierType::IDENTIFIER_FUNCTION));

        $functionInfo = $this->mainSourceLocator->locateIdentifier($this, $identifier);

        if ($functionInfo === null) {
            $functionInfo = $this->secondarySourceLocator->locateIdentifier($this, $identifier);
        }

        if ($functionInfo === null) {
            throw IdentifierNotFound::fromIdentifier($identifier);
        }

        assert($functionInfo instanceof ReflectionFunction);

        return $functionInfo;
    }

    /**
     * Get all the functions available in the scope specified by the MainSourceLocator.
     *
     * @return list<ReflectionFunction>
     */
    public function reflectAllFunctions(): iterable
    {
        /** @var list<ReflectionFunction> $allFunctions */
        $allFunctions = $this->mainSourceLocator->locateIdentifiersByType(
            $this,
            new IdentifierType(IdentifierType::IDENTIFIER_FUNCTION),
        );

        return $allFunctions;
    }

    /**
     * Create a ReflectionConstant for the specified $constantName.
     *
     * @throws IdentifierNotFound
     */
    public function reflectConstant(string $identifierName): ReflectionConstant
    {
        $identifier = new Identifier($identifierName, new IdentifierType(IdentifierType::IDENTIFIER_CONSTANT));

        $constantInfo = $this->mainSourceLocator->locateIdentifier($this, $identifier);

        if ($constantInfo === null) {
            $constantInfo = $this->secondarySourceLocator->locateIdentifier($this, $identifier);
        }

        if ($constantInfo === null) {
            throw IdentifierNotFound::fromIdentifier($identifier);
        }

        assert($constantInfo instanceof ReflectionConstant);

        return $constantInfo;
    }

    /**
     * Get all the constants available in the scope specified by the MainSourceLocator.
     *
     * @return list<ReflectionConstant>
     */
    public function reflectAllConstants(): iterable
    {
        /** @var list<ReflectionConstant> $allConstants */
        $allConstants = $this->mainSourceLocator->locateIdentifiersByType(
            $this,
            new IdentifierType(IdentifierType::IDENTIFIER_CONSTANT),
        );

        return $allConstants;
    }
}
