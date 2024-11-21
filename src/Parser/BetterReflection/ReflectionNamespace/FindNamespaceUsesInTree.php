<?php

declare(strict_types=1);

namespace setasign\PhpStubGenerator\Parser\BetterReflection\ReflectionNamespace;

use PhpParser\Node;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Roave\BetterReflection\Identifier\IdentifierType;

final class FindNamespaceUsesInTree
{
    public function __invoke(array $ast, IdentifierType $identifierType): array
    {
        $nodeVisitor = new class ($identifierType) extends NodeVisitorAbstract {
            private array $uses = [];
            private ?Namespace_ $currentNamespace = null;
            private array $currentUnassignedUses = [];

            public function __construct(private readonly IdentifierType $identifierType)
            {
            }

            /**
             * {@inheritDoc}
             */
            public function enterNode(Node $node)
            {
                if ($node instanceof Namespace_) {
                    $this->currentNamespace = $node;
                }

                return null;
            }

            /**
             * {@inheritDoc}
             */
            public function leaveNode(Node $node)
            {
                if ($node instanceof Use_ && $this->currentNamespace === null) {
                    $this->currentUnassignedUses[] = $node;

                    return null;
                }

                if (
                    ($this->identifierType->isClass() && $node instanceof Node\Stmt\ClassLike && $node->name !== null)
                    || ($this->identifierType->isFunction() && $node instanceof Node\Stmt\Function_)
                ) {
                    if ($this->currentNamespace === null) {
                        $this->currentNamespace = new Namespace_(null, $this->currentUnassignedUses);
                    }

                    if ($this->currentNamespace->name !== null) {
                        $name = $this->currentNamespace->name->toString() . '\\' . $node->name->toString();
                    } else {
                        $name = $node->name->toString();
                    }

                    $this->uses[$name] = (new ReflectionFileNamespace($this->currentNamespace))->getNamespaceAliases();

                    return null;
                }

//                if ($this->identifierType->isConstant() && $node instanceof Node\Stmt\Const_) {
//                }

                if ($node instanceof Namespace_) {
                    $this->currentNamespace = null;
                }

                return null;
            }

            public function getAliases(): array
            {
                return $this->uses;
            }
        };

        $nodeTraverser = new NodeTraverser($nodeVisitor);
        $nodeTraverser->traverse($ast);

        return $nodeVisitor->getAliases();
    }
}
