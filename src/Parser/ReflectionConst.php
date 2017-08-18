<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Parser;

use Go\ParserReflection\ReflectionEngine;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\Namespace_;

/**
 * We need this class in the annotation reader because there is no builtin constant reflector
 * We only implemented getDocComment because that's the only feature we need
 *
 * @package com\setasign\SetaSite\CustomModules\ApiDoc
 * @license MIT https://github.com/marcioAlmada/annotations
 * @copyright 2013-2014 Márcio Almada
 * @copyright 2016 Setasign
 */
class ReflectionConst implements \Reflector
{
    protected $declaringClass;
    protected $classConstNode;
    protected $constNode;
    protected $docComment;
    private $docCommentProcessed = false;

    /**
     * @param \ReflectionClass $classReflection
     * @param string $constName name of the constant
     * @throws \ReflectionException
     */
    public function __construct(\ReflectionClass $classReflection, string $constName)
    {
        $this->declaringClass = $classReflection;
        $className = $classReflection->getName();
        $fileName = $classReflection->getFileName();

        $stmts = ReflectionEngine::parseFile($fileName);

        // Class can be in a namespace or at the root of the statement
        $classNode = $this->findClassNode($stmts);
        if (!$classNode) {
            throw new \ReflectionException("Class ${className} not found in file ${fileName}");
        }

        // Find the constant we are looking for
        foreach ($classNode->stmts as $classSubNode) {
            if ($classSubNode instanceof ClassConst) {
                foreach ($classSubNode->consts as $constNode) {
                    if ($constNode->name === $constName) {
                        $this->classConstNode = $classSubNode;
                        $this->constNode = $constNode;
                        break 2;
                    }
                }
            }
        }

        if (!$this->constNode) {
            throw new \ReflectionException("Class constant ${constName} does not exist in class ${className}");
        }
    }

    public function getDeclaringClass()
    {
        return $this->declaringClass;
    }

    /**
     * @param $stmts
     * @return Class_
     */
    private function findClassNode($stmts)
    {
        foreach ($stmts as $node) {
            if ($node instanceof Namespace_) {
                return $this->findClassNode($node->stmts);
            }

            if ($node instanceof Class_) {
                return $node;
            }
        }
        return null;
    }
    /**
     * @return string
     */
    public function getDocComment()
    {
        if (false === $this->docCommentProcessed) {
            $this->docComment = null;
            /**
             *
             * The first constant can have additional docblock
             *
             * /**
             *  * This belongs to the first
             *  * /
             * const
             *
             *      FOO = 'foo',
             *      BAR = 'bar'
             *
             *
             * const
             *      /**
             *       * This belongs to the first
             *       * /
             *      FOO = "foo";
             *
             */
            // Then we take every comments from the constant node
            // Then if it's the first of the list we tank everything from the classConstNode
            // (and we order it from the closest to the further
            $comments = array_reverse($this->constNode->getAttribute('comments', []));
            if ($this->classConstNode->consts[0] == $this->constNode) {
                $comments += array_reverse($this->classConstNode->getAttribute('comments', []));
            }
            if (count($comments) > 0) {
                // we can have many doc comment for one statement
                // We only take the closest one
                while ($this->docComment === null && $currentComment = current($comments)) {
                    if (strpos((string)$currentComment, '/**') === 0) {
                        $this->docComment = (string)$currentComment;
                    }
                    next($comments);
                }
            }
            $this->docCommentProcessed = true;
        }
        return $this->docComment;
    }
    /**
     * No need to implement it, we only need the getDocComment
     */
    public static function export()
    {
    }
    /**
     * No need to implement it, we only need the getDocComment
     */
    public function __toString()
    {
    }
}