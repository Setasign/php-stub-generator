<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Formatter;

use ReflectionFunctionAbstract;
use ReflectionType;
use setasign\PhpStubGenerator\Helper\FormatHelper;
use setasign\PhpStubGenerator\PhpStubGenerator;

class FunctionFormatter
{
    const DEFAULT_TYPES = ['int', 'float', 'bool', 'string', 'self', 'callable', 'array', 'object'];

    /**
     * @var ReflectionFunctionAbstract
     */
    protected $function;

    public function __construct(ReflectionFunctionAbstract $function)
    {
        $this->function = $function;
    }

    protected function formatParams(): string
    {
        $params = [];
        foreach ($this->function->getParameters() as $parameter) {
            $param = '';
            $type = (string) $parameter->getType();

            if ($type !== '') {
                if ($parameter->allowsNull() &&
                    (!$parameter->isDefaultValueAvailable() || $parameter->getDefaultValue() !== null)
                ) {
                    $param .= '?';
                }

                if (in_array($type, self::DEFAULT_TYPES, true)) {
                    $param .= $type;
                } else {
                    $param .= '\\' . ltrim($type, '\\');
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

        return implode(', ', $params);
    }

    protected function formatReturnType(): string
    {
        $result = '';

        if ($this->function->hasReturnType()) {
            $returnType = $this->function->getReturnType();
            if ($returnType instanceof ReflectionType) {
                $allowsNull = $returnType->allowsNull();
                $returnType = (string) $returnType;

                $result .= ': ' . ($allowsNull ? '?' : '');
                if (in_array($returnType, self::DEFAULT_TYPES, true)) {
                    $result .= $returnType;
                } else {
                    $result .= '\\' . ltrim($returnType, '\\');
                }
            }
        }

        return $result;
    }

    public function format(): string
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $result = '';
        $result .= FormatHelper::indentDocBlock((string) $this->function->getDocComment(), 1, $t) . $n
            . $t;

        $result .= 'function ' . $this->function->getName() . '(' . $this->formatParams() . ')';
        $this->formatReturnType();

        $result .= ' {}' . $n . $n;

        return $result;
    }
}
