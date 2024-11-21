<?php

declare(strict_types=1);

namespace setasign\PhpStubGenerator\Parser\BetterReflection\ReflectionNamespace;

use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;

/**
 * AST-based reflection for the concrete namespace in the file
 */
class ReflectionFileNamespace
{
    /**
     * List of imported namespaces (aliases)
     *
     * @var array
     */
    protected array $fileNamespaceAliases;

    /**
     * Namespace node
     *
     * @var Namespace_
     */
    private Namespace_ $namespaceNode;

    /**
     * File namespace constructor
     *
     * @param Namespace_ $namespaceNode AST-node for this namespace block
     */
    public function __construct(Namespace_ $namespaceNode)
    {
        $this->namespaceNode = $namespaceNode;
    }

    /**
     * Gets namespace name
     */
    public function getName(): string
    {
        $nameNode = $this->namespaceNode->name;

        return $nameNode ? $nameNode->toString() : '';
    }

    /**
     * Returns a list of namespace aliases
     */
    public function getNamespaceAliases(): array
    {
        if (!isset($this->fileNamespaceAliases)) {
            $this->fileNamespaceAliases = $this->findNamespaceAliases();
        }

        return $this->fileNamespaceAliases;
    }

    /**
     * Returns an AST-node for namespace
     */
    public function getNode(): ?Namespace_
    {
        return $this->namespaceNode;
    }

    /**
     * Searches for namespace aliases for the current block
     */
    private function findNamespaceAliases(): array
    {
        $namespaceAliases = [];

        // aliases can be only top-level nodes in the namespace, so we can scan them directly
        foreach ($this->namespaceNode->stmts as $namespaceLevelNode) {
            if ($namespaceLevelNode instanceof Use_) {
                $useAliases = $namespaceLevelNode->uses;
                if (!empty($useAliases)) {
                    foreach ($useAliases as $useNode) {
                        $namespaceAliases[$useNode->name->toString()] = (string) $useNode->getAlias();
                    }
                }
            }
        }

        return $namespaceAliases;
    }
}
