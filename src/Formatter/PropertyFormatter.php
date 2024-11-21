<?php

declare(strict_types=1);

namespace setasign\PhpStubGenerator\Formatter;

use ReflectionProperty;
use setasign\PhpStubGenerator\Helper\FormatHelper;
use setasign\PhpStubGenerator\PhpStubGenerator;

class PropertyFormatter
{
    protected string $className;

    protected ReflectionProperty $property;

    /**
     * @var mixed
     */
    protected mixed $defaultValue;

    /**
     * PropertyFormatter constructor.
     *
     * @param string $className
     * @param ReflectionProperty $property
     * @param mixed $defaultValue
     */
    public function __construct(string $className, ReflectionProperty $property, mixed $defaultValue)
    {
        $this->className = $className;
        $this->property = $property;
        $this->defaultValue = $defaultValue;
    }

    /**
     * @return string
     */
    public function format(): string
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        if (
            !$this->property->isDefault()
            || $this->property->getDeclaringClass()->getName() !== $this->className
        ) {
            return '';
        }

        $result = '';
        $doc = $this->property->getDocComment();
        if (\is_string($doc)) {
            $result .= FormatHelper::indentDocBlock($doc, 2, $t) . $n;
        }

        $result .= $t . $t;
        if ($this->property->isPublic()) {
            $result .= 'public ';
        } elseif ($this->property->isProtected()) {
            $result .= 'protected ';
        } elseif ($this->property->isPrivate()) {
            $result .= 'private ';
        }

        if ($this->property->isStatic()) {
            $result .= 'static ';
        }

        $result .= '$' . $this->property->getName();
        $formattedValue = FormatHelper::formatValue($this->defaultValue);
        if ($formattedValue !== 'null') {
            $result .= ' = ' . $formattedValue;
        }

        $result .= ';' . $n . $n;

        return $result;
    }
}
