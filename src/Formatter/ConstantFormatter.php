<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Formatter;

use ReflectionClass;
use setasign\PhpStubGenerator\Helper\FormatHelper;
use setasign\PhpStubGenerator\Parser\ParserInterface;
use setasign\PhpStubGenerator\Parser\ReflectionConst;
use setasign\PhpStubGenerator\PhpStubGenerator;

class ConstantFormatter
{
    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * @var ReflectionClass
     */
    private $class;

    /**
     * @var string
     */
    private $constantName;

    public function __construct(ParserInterface $parser, ReflectionClass $class, string $constantName)
    {
        $this->parser = $parser;
        $this->class = $class;
        $this->constantName = $constantName;
    }

    public function format(): string
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $parentClass = null;
        try {
            $parentClass = $this->class->getParentClass();
        } catch (\Throwable $e) {
        }
        $value = $this->class->getConstant($this->constantName);

        if ($parentClass instanceof ReflectionClass && $parentClass->hasConstant($this->constantName)
            && $parentClass->getConstant($this->constantName) === $value
        ) {
            return '';
        }

        $isDeclaredInInterface = false;
        foreach ($this->class->getInterfaces() as $interface) {
            if ($interface->hasConstant($this->constantName)
                && $interface->getConstant($this->constantName) === $value
            ) {
                $isDeclaredInInterface = true;
                continue;
            }
        }

        if ($isDeclaredInInterface) {
            return '';
        }

        $reflectionConst = $this->parser->getConstantReflection($this->class, $this->constantName);

        $result = '';
        if ($reflectionConst instanceof ReflectionConst) {
            $docComment = $reflectionConst->getDocComment();
            if (is_string($docComment)) {
                $result .= FormatHelper::indentDocBlock($docComment, 2, $t) . $n;
            }
        }

        $value = FormatHelper::formatValue($value);
        $result .= $t . $t
            . 'const ' . $this->constantName . ' = ' . $value . ';'
            . $n . $n;

        return $result;
    }
}
