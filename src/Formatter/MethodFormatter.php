<?php

declare(strict_types=1);

namespace setasign\PhpStubGenerator\Formatter;

use ReflectionMethod;
use ReturnTypeWillChange;
use setasign\PhpStubGenerator\Helper\FormatHelper;
use setasign\PhpStubGenerator\PhpStubGenerator;

class MethodFormatter extends FunctionFormatter
{
    private string $className;

    private bool $classIsInterface;

    /**
     * MethodFormatter constructor.
     *
     * @param string $className
     * @param bool $classIsInterface
     * @param ReflectionMethod $method
     */
    public function __construct(string $className, bool $classIsInterface, ReflectionMethod $method)
    {
        $this->className = $className;
        $this->classIsInterface = $classIsInterface;
        $this->function = $method;
    }

    /**
     * @return string
     */
    public function format(): string
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        if ($this->function->getDeclaringClass()->getName() !== $this->className) {
            return '';
        }

        $result = '';
        $doc = $this->function->getDocComment();
        if (\is_string($doc)) {
            $result .= FormatHelper::indentDocBlock($doc, 2, $t) . $n;
        }

        $attributes = $this->function->getAttributes();
        foreach ($attributes as $attribute) {
            $result .= $t . '#[' . $attribute->getName();
            if ($attribute->getArguments() !== []) {
                $arguments = \array_map([FormatHelper::class, 'formatValue'], $attribute->getArguments());
                $result .= '(' . \implode(', ', $arguments) . ')';
            }
            $result .= ']' . $n;
        }

        $result .= $t . $t;
        if (!$this->classIsInterface && $this->function->isAbstract()) {
            $result .= 'abstract ';
        } elseif ($this->function->isFinal()) {
            $result .= 'final ';
        }

        if ($this->function->isPublic()) {
            $result .= 'public ';
        } elseif ($this->function->isProtected()) {
            $result .= 'protected ';
        } elseif ($this->function->isPrivate()) {
            $result .= 'private ';
        }

        if ($this->function->isStatic()) {
            $result .= 'static ';
        }

        $result .= 'function ' . $this->function->getName() . '(' . $this->formatParams() . ')'
            . $this->formatReturnType();

        if (!$this->classIsInterface && !$this->function->isAbstract()) {
            $result .= ' {}' . $n . $n;
        } else {
            $result .= ';' . $n . $n;
        }

        return $result;
    }
}
