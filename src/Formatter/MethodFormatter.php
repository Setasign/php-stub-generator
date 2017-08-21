<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Formatter;

use ReflectionClass;
use ReflectionMethod;
use ReflectionType;
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
    const DEFAULT_TYPES = ['int', 'float', 'bool', 'string', 'self', 'callable', 'array', 'object'];

    /**
     * @var string
     */
    private $className;

    /**
     * @var bool
     */
    private $classIsInterface;

    public function __construct(string $className, bool $classIsInterface, ReflectionMethod $method)
    {
        $this->className = $className;
        $this->classIsInterface = $classIsInterface;
        parent::__construct($method);
    }

    public function format(): string
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        if ($this->function->getDeclaringClass()->getName() !== $this->className) {
            return '';
        }

        $result = '';
        $result .= FormatHelper::indentDocBlock((string) $this->function->getDocComment(), 2, $t) . $n
            . $t . $t;

        if (!$this->classIsInterface && $this->function->isAbstract()) {
            $result .= 'abstract ';
        } elseif ($this->function->isFinal()) {
            $result .= 'final ';
        }

        if ($this->function->isPublic()) {
            $result .= 'public ';
        } elseif ($this->function->isProtected()) {
            $result .= 'protected ';
        } else {
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
