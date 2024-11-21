<?php

declare(strict_types=1);

namespace setasign\PhpStubGenerator\Formatter;

use ReflectionFunction;
use ReflectionMethod;
use ReflectionType;
use ReflectionUnionType;
use setasign\PhpStubGenerator\Helper\FormatHelper;
use setasign\PhpStubGenerator\PhpStubGenerator;

class FunctionFormatter
{
    public const DEFAULT_TYPES = [
        'null', 'int', 'float', 'bool', 'string', 'self', 'callable', 'array', 'object', 'mixed'
    ];

    protected ReflectionFunction|ReflectionMethod $function;

    /**
     * FunctionFormatter constructor.
     *
     * @param ReflectionFunction $function
     */
    public function __construct(ReflectionFunction $function)
    {
        $this->function = $function;
    }

    protected function formatType(ReflectionType|null $type): string
    {
        if ($type instanceof ReflectionUnionType) {
            $unionTypes = \array_map([$this, 'formatType'], $type->getTypes());
            $unionTypesWithoutNull = \array_filter($unionTypes, function (string $type) {
                return $type !== 'null';
            });
            if ($type->allowsNull() && count($unionTypesWithoutNull) === 1) {
                $typeAllowsNull = true;
                $type = \implode('|', $unionTypesWithoutNull);
            } else {
                $typeAllowsNull = false;
                $type = \implode('|', $unionTypes);
            }
        } else {
            $typeAllowsNull = $type->allowsNull();
            $type = \ltrim((string) $type, '\\?');
            if (!\in_array($type, self::DEFAULT_TYPES, true)) {
                $type = '\\' . $type;
            }
        }

        if ($type === 'null' || $type === 'mixed') {
            return $type;
        }

        return ($typeAllowsNull ? '?' : '') . $type;
    }

    /**
     * @return string
     */
    protected function formatParams(): string
    {
        $params = [];
        foreach ($this->function->getParameters() as $parameter) {
            $param = '';
            $type = $parameter->getType();
            if ($type instanceof ReflectionType) {
                $param .= $this->formatType($type) . ' ';
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
            $result .= ': ' . $this->formatType($this->function->getReturnType());
        }

        return $result;
    }

    /**
     * @return string
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
