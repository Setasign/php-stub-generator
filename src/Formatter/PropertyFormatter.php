<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Formatter;

use ReflectionClass;
use ReflectionProperty;
use setasign\PhpStubGenerator\Helper\FormatHelper;
use setasign\PhpStubGenerator\PhpStubGenerator;

class PropertyFormatter
{
    /**
     * @var ReflectionClass
     */
    protected $class;

    /**
     * @var ReflectionProperty
     */
    protected $property;

    public function __construct(ReflectionClass $class, ReflectionProperty $property)
    {
        $this->class = $class;
        $this->property = $property;
    }

    public function format(): string
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        if (!$this->property->isDefault()
            || $this->property->getDeclaringClass()->getName() !== $this->class->getName()
        ) {
            return '';
        }

        $result = '';
        $result .= FormatHelper::indentDocBlock((string) $this->property->getDocComment(), 2, $t) . $n
            . $t . $t;

        if ($this->property->isPublic()) {
            $result .= 'public ';
        } elseif ($this->property->isProtected()) {
            $result .= 'protected ';
        } else {
            $result .= 'private ';
        }

        if ($this->property->isStatic()) {
            $result .= 'static ';
        }

        $result .= '$' . $this->property->getName();
        $formattedValue = FormatHelper::formatValue($this->property->getValue());
        if ($formattedValue !== 'null') {
            $result .= ' = ' . $formattedValue;
        }

        $result .= ';' . $n . $n;

        return $result;
    }
}
