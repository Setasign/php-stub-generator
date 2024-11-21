<?php

declare(strict_types=1);

namespace setasign\PhpStubGenerator\Parser\BetterReflection;

use Roave\BetterReflection\Identifier\Identifier;
use Roave\BetterReflection\Identifier\IdentifierType;
use Roave\BetterReflection\Reflection\Reflection;
use Roave\BetterReflection\Reflector\Reflector;
use Roave\BetterReflection\SourceLocator\Ast\Locator as AstLocator;
use Roave\BetterReflection\SourceLocator\Located\LocatedSource;
use Roave\BetterReflection\SourceLocator\Type\SourceLocator;

class FileListSourceLocator implements SourceLocator
{
    private array $memoize = [];

    private array $remainingFiles = [];

    public function __construct(private array $fileList, private AstLocator $astLocator)
    {
    }

    protected function buildFileMap(IdentifierType $identifierType)
    {
        $this->memoize[$identifierType->getName()] = [];
        $this->remainingFiles[$identifierType->getName()] = $this->fileList;
    }

    protected function continueBuildMap(Reflector $reflector, IdentifierType $identifierType)
    {
        while (
            \array_key_exists($identifierType->getName(), $this->remainingFiles)
            && $file = array_shift($this->remainingFiles[$identifierType->getName()])
        ) {
            $locatedSource = new LocatedSource(
                file_get_contents($file),
                '*',
                $file,
            );

            $reflections = $this->astLocator->findReflectionsOfType(
                $reflector,
                $locatedSource,
                $identifierType,
            );

            foreach ($reflections as $reflection) {
                $this->memoize[$identifierType->getName()][$reflection->getName()] = $reflection;
            }
        }
        unset($this->remainingFiles[$identifierType->getName()]);
    }

    public function locateIdentifier(Reflector $reflector, Identifier $identifier): ?Reflection
    {
        if (!\array_key_exists($identifier->getType()->getName(), $this->memoize)) {
            $this->buildFileMap($identifier->getType());
        }
        if (\array_key_exists($identifier->getType()->getName(), $this->remainingFiles)) {
            $this->continueBuildMap($reflector, $identifier->getType());
        }

        if (\array_key_exists($identifier->getName(), $this->memoize[$identifier->getType()->getName()])) {
            return $this->memoize[$identifier->getType()->getName()][$identifier->getName()];
        }

        return null;
    }

    public function locateIdentifiersByType(Reflector $reflector, IdentifierType $identifierType): array
    {
        if (!\array_key_exists($identifierType->getName(), $this->memoize)) {
            $this->buildFileMap($identifierType);
        }
        if (\array_key_exists($identifierType->getName(), $this->remainingFiles)) {
            $this->continueBuildMap($reflector, $identifierType);
        }

        return $this->memoize[$identifierType->getName()];
    }
}
