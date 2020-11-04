<?php

declare(strict_types=1);

namespace setasign\PhpStubGenerator\Formatter;

use ReflectionMethod;
use setasign\PhpStubGenerator\Helper\FormatHelper;
use setasign\PhpStubGenerator\PhpStubGenerator;

/**
 * Class MethodFormatter
 *
 * @package setasign\PhpStubGenerator\Formatter
 * @property ReflectionMethod $function
 */
class MethodFormatter extends FunctionFormatter
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var bool
     */
    private $classIsInterface;

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
        parent::__construct($method);
    }

    /**
     * @return string
     * @throws \ReflectionException
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
