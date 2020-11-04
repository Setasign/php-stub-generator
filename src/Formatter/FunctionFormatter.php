<?php

declare(strict_types=1);

namespace setasign\PhpStubGenerator\Formatter;

use ReflectionFunctionAbstract;
use ReflectionType;
use setasign\PhpStubGenerator\Helper\FormatHelper;
use setasign\PhpStubGenerator\PhpStubGenerator;

class FunctionFormatter
{
    public const DEFAULT_TYPES = ['int', 'float', 'bool', 'string', 'self', 'callable', 'array', 'object'];

    /**
     * @var ReflectionFunctionAbstract
     */
    protected $function;

    /**
     * FunctionFormatter constructor.
     *
     * @param ReflectionFunctionAbstract $function
     */
    public function __construct(ReflectionFunctionAbstract $function)
    {
        $this->function = $function;
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    protected function formatParams(): string
    {
        $params = [];
        foreach ($this->function->getParameters() as $parameter) {
            $param = '';
            $type = $parameter->getType();
            $typeAllowsNull = false;
            if ($type instanceof ReflectionType) {
                $typeAllowsNull = $type->allowsNull();
                $type = (string) $type;
            } else {
                $type = '';
            }

            if ($type !== '') {
                if ($typeAllowsNull) {
                    $param .= '?';
                }

                if (\in_array($type, self::DEFAULT_TYPES, true)) {
                    $param .= $type;
                } else {
                    $param .= '\\' . \ltrim($type, '\\');
                }

                $param .= ' ';
            }

            if ($parameter->isPassedByReference()) {
                $param .= '&';
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

        return \implode(', ', $params);
    }

    /**
     * @return string
     */
    protected function formatReturnType(): string
    {
        $result = '';

        if ($this->function->hasReturnType()) {
            $returnType = $this->function->getReturnType();

            if ($returnType instanceof ReflectionType) {
                $allowsNull = $returnType->allowsNull();
                $returnType = (string) $returnType;

                $result .= ': ' . ($allowsNull ? '?' : '');
                if (\in_array($returnType, self::DEFAULT_TYPES, true)) {
                    $result .= $returnType;
                } else {
                    $result .= '\\' . \ltrim($returnType, '\\');
                }
            }
        }

        return $result;
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public function format(): string
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $result = '';
        $doc = $this->function->getDocComment();
        if (\is_string($doc)) {
            $result .= FormatHelper::indentDocBlock($doc, 1, $t) . $n;
        }

        $result .= $t . 'function ' . $this->function->getName() . '(' . $this->formatParams() . ')';
        $result .= $this->formatReturnType();

        $result .= ' {}' . $n . $n;

        return $result;
    }
}
