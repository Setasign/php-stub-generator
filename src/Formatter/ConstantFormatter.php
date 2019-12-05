<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Formatter;

use setasign\PhpStubGenerator\Helper\FormatHelper;
use setasign\PhpStubGenerator\PhpStubGenerator;

class ConstantFormatter
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var \ReflectionClassConstant
     */
    private $reflectionClassConstant;

    /**
     * ConstantFormatter constructor.
     *
     * @param string $className
     * @param \ReflectionClassConstant $reflectionClassConstant
     */
    public function __construct(string $className, \ReflectionClassConstant $reflectionClassConstant)
    {
        $this->className = $className;
        $this->reflectionClassConstant = $reflectionClassConstant;
    }

    /**
     * @return string
     */
    public function format(): string
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        if ($this->reflectionClassConstant->getDeclaringClass()->getName() !== $this->className) {
            return '';
        }
        $value = $this->reflectionClassConstant->getValue();

        $result = '';
        $docComment = $this->reflectionClassConstant->getDocComment();
        if (\is_string($docComment)) {
            $result .= FormatHelper::indentDocBlock($docComment, 2, $t) . $n;
        }

        $visibility = '';
        if (PhpStubGenerator::$addClassConstantsVisibility) {
            if ($this->reflectionClassConstant->isPublic()) {
                $visibility = 'public ';
            } elseif ($this->reflectionClassConstant->isProtected()) {
                $visibility = 'protected ';
            } elseif ($this->reflectionClassConstant->isPrivate()) {
                $visibility = 'private ';
            }
        }

        $value = FormatHelper::formatValue($value);
        $result .= $t . $t
            . $visibility
            . 'const ' . $this->reflectionClassConstant->getName() . ' = ' . $value . ';'
            . $n . $n;

        return $result;
    }
}
