<?php

declare(strict_types=1);

namespace setasign\PhpStubGenerator\Parser\BetterReflection;

use PhpParser\Parser;
use Roave\BetterReflection\Identifier\IdentifierType;
use Roave\BetterReflection\Reflection\Reflection;
use Roave\BetterReflection\Reflector\Reflector;
use Roave\BetterReflection\SourceLocator\Ast\Exception\ParseToAstFailure;
use Roave\BetterReflection\SourceLocator\Ast\FindReflectionsInTree;
use Roave\BetterReflection\SourceLocator\Ast\Locator;
use Roave\BetterReflection\SourceLocator\Ast\Strategy\NodeToReflection;
use Roave\BetterReflection\SourceLocator\Located\LocatedSource;
use setasign\PhpStubGenerator\Parser\BetterReflection\ReflectionNamespace\FindNamespaceUsesInTree;
use Throwable;

class AstLocatorWithNamespaceUses extends Locator
{
    private FindReflectionsInTree $findReflectionsInTree;
    private FindNamespaceUsesInTree $findNamespaceUsesInTree;
    private array $namespaceUses = [];

    public function __construct(private Parser $parser)
    {
        $this->findReflectionsInTree = new FindReflectionsInTree(new NodeToReflection());
        $this->findNamespaceUsesInTree = new FindNamespaceUsesInTree();
    }

    /**
     * Get an array of reflections found in some code.
     *
     * @return list<Reflection>
     *
     * @throws ParseToAstFailure
     */
    public function findReflectionsOfType(
        Reflector $reflector,
        LocatedSource $locatedSource,
        IdentifierType $identifierType,
    ): array {
        try {
            $ast = $this->parser->parse($locatedSource->getSource());
            $namespaceUses = $this->findNamespaceUsesInTree->__invoke($ast, $identifierType);
            foreach ($namespaceUses as $className => $uses) {
                $this->namespaceUses[$identifierType->getName()][$className] = $uses;
            }

            return $this->findReflectionsInTree->__invoke(
                $reflector,
                $ast,
                $identifierType,
                $locatedSource,
            );
        } catch (Throwable $exception) {
            throw ParseToAstFailure::fromLocatedSource($locatedSource, $exception);
        }
    }

    public function getNamespaceUses(IdentifierType $identifierType, string $name): array
    {
        return $this->namespaceUses[$identifierType->getName()][$name] ?? [];
    }
}
