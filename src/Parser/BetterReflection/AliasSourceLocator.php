<?php

declare(strict_types=1);

namespace setasign\PhpStubGenerator\Parser\BetterReflection;

use Roave\BetterReflection\Identifier\Identifier;
use Roave\BetterReflection\Identifier\IdentifierType;
use Roave\BetterReflection\Reflection\Reflection;
use Roave\BetterReflection\Reflector\Reflector;
use Roave\BetterReflection\SourceLocator\Type\SourceLocator;

class AliasSourceLocator implements SourceLocator
{
    public function __construct(private SourceLocator $redirectSourceLocator, private array &$classAliases)
    {
    }

    /**
     * @inheritDoc
     */
    public function locateIdentifier(Reflector $reflector, Identifier $identifier): Reflection|null
    {
        if (!$identifier->isClass() || !\array_key_exists($identifier->getName(), $this->classAliases)) {
            return null;
        }

        return $this->redirectSourceLocator->locateIdentifier(
            $reflector,
            new Identifier($this->classAliases[$identifier->getName()], $identifier->getType())
        );
    }

    /**
     * @inheritDoc
     */
    public function locateIdentifiersByType(Reflector $reflector, IdentifierType $identifierType): array
    {
        return [];
    }
}
