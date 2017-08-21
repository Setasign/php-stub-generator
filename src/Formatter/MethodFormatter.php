<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Formatter;

use ReflectionClass;
use ReflectionMethod;
use ReflectionType;
use setasign\PhpStubGenerator\Helper\FormatHelper;
use setasign\PhpStubGenerator\PhpStubGenerator;

class MethodFormatter
{
    /**
     * @var ReflectionClass
     */
    private $class;

    /**
     * @var ReflectionMethod
     */
    private $method;

    public function __construct(ReflectionClass $class, ReflectionMethod $method)
    {
        $this->class = $class;
        $this->method = $method;
    }

    public function format(): string
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        if ($this->method->getDeclaringClass()->getName() !== $this->class->getName()) {
            return '';
        }

        $result = '';
        $result .= FormatHelper::indentDocBlock((string) $this->method->getDocComment(), 2, $t) . $n
            . $t . $t;

        if ($this->method->isAbstract() && !$this->class->isInterface()) {
            $result .= 'abstract ';
        } elseif ($this->method->isFinal()) {
            $result .= 'final ';
        }

        if ($this->method->isPublic()) {
            $result .= 'public ';
        } elseif ($this->method->isProtected()) {
            $result .= 'protected ';
        } else {
            $result .= 'private ';
        }

        if ($this->method->isStatic()) {
            $result .= 'static ';
        }

        $result .= 'function ' . $this->method->getName() . '(';
        $params = [];
        foreach ($this->method->getParameters() as $parameter) {
            $param = '';
            $type = (string) $parameter->getType();

            if ($type !== '') {
                $param .= $type;

                if ($parameter->allowsNull() &&
                    (!$parameter->isDefaultValueAvailable() || $parameter->getDefaultValue() !== null)
                ) {
                    $param .= '?';
                }

                $param .= ' ';
            }

            if ($parameter->isVariadic()) {
                $param .= '...';
            }

            $param .= '$' . $parameter->getName();
            if ($parameter->isDefaultValueAvailable()) {
                if ($parameter->isDefaultValueConstant()) {
                    $default = $parameter->getDefaultValueConstantName();
                } else {
                    $default = FormatHelper::formatValue($parameter->getDefaultValue());
                }

                $param .= ' = ' . $default;
            }
            $params[] = $param;

            unset($default);
        }
        $result .= implode(', ', $params);
        $result .= ')';

        if ($this->method->hasReturnType()) {
            $returnType = $this->method->getReturnType();
            if ($returnType instanceof ReflectionType) {
                $allowsNull = $returnType->allowsNull();

                $result .= ':' . ($allowsNull ? '?' : '') . $returnType;
            }
        }

        if (!$this->class->isInterface() && !$this->method->isAbstract()) {
            $result .= ' {}' . $n . $n;
        } else {
            $result .= ';' . $n . $n;
        }

        return $result;
    }
}
