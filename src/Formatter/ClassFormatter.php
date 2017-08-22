<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Formatter;

use ReflectionClass;
use setasign\PhpStubGenerator\Helper\FormatHelper;
use setasign\PhpStubGenerator\Parser\ParserInterface;
use setasign\PhpStubGenerator\PhpStubGenerator;

class ClassFormatter
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
     * ClassFormatter constructor.
     *
     * @param ParserInterface $parser
     * @param ReflectionClass $class
     */
    public function __construct(ParserInterface $parser, ReflectionClass $class)
    {
        $this->parser = $parser;
        $this->class = $class;
    }

    public function format(bool $ignoreSubElements = false): string
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $result = '';
        $docComment = $this->class->getDocComment();
        if ($docComment !== false) {
            $result .= FormatHelper::indentDocBlock($docComment, 1, $t) . $n;
        }
        $result .= $t;

        if ($this->class->isInterface()) {
            $result .= 'interface ';
        } elseif ($this->class->isTrait()) {
            $result .= 'trait ';
        } else {
            if ($this->class->isAbstract()) {
                $result .= 'abstract ';
            } elseif ($this->class->isFinal()) {
                $result .= 'final ';
            }

            $result .= 'class ';
        }
        $result .= $this->class->getShortName();
        $parentClass = null;
        try {
            $parentClass = $this->class->getParentClass();
        } catch (\Throwable $e) {
        }
        if ($parentClass instanceof ReflectionClass) {
            $result .= ' extends \\' . ltrim($parentClass->getName(), '\\');
        }

        $interfaces = $this->class->getInterfaces();
        // remove interfaces from parent class if there is a parent class
        if ($parentClass instanceof ReflectionClass) {
            $interfaces = array_filter($interfaces, function (ReflectionClass $interface) use ($parentClass) {
                // if the $parentClass is a default php class it cannot use an user defined interface
                if (!$parentClass->isUserDefined() && $interface->isUserDefined()) {
                    return false;
                }

                return !$parentClass->implementsInterface($interface->getName());
            });
        }

        // remove sub interfaces of other interfaces
        $interfaces = array_filter($interfaces, function (ReflectionClass $interface) use ($interfaces) {
            $interfaceName = $interface->getName();
            foreach ($interfaces as $compareInterface) {
                /**
                 * @var ReflectionClass $compareInterface
                 */
                /** @noinspection NotOptimalIfConditionsInspection */
                // if the $compareInterface is a default php interface it cannot use an user defined interface
                if ((!$compareInterface->isUserDefined() && $interface->isUserDefined())
                    || $compareInterface->implementsInterface($interfaceName)
                ) {
                    return false;
                }
            }
            return true;
        });

        $interfaceNames = array_map(function (ReflectionClass $interface) {
            return $interface->getName();
        }, $interfaces);

        if (count($interfaceNames) > 0) {
            if ($this->class->isInterface()) {
                $result .= ' extends ';
            } else {
                $result .= ' implements ';
            }

            $interfaceNames = array_map(function ($interfaceName) {
                return '\\' . ltrim($interfaceName, '\\');
            }, $interfaceNames);

            $result .= implode(', ', $interfaceNames);
        }

        $result .= $n
            . $t . '{' . $n;

        if (!$ignoreSubElements) {
            // todo implement traitUseBlockFormatter
//            $result .= (new TraitUseBlockFormatter($this->class))->format();

            foreach ($this->class->getConstants() as $constantName => $constantValue) {
                $result .= (new ConstantFormatter($this->parser, $this->class, $constantName))->format();
            }

            foreach ($this->class->getProperties() as $property) {
                $result .= (new PropertyFormatter($this->class->getName(), $property))->format();
            }

            foreach ($this->class->getMethods() as $method) {
                $result .= (new MethodFormatter($this->class->getName(), $this->class->isInterface(), $method))
                    ->format();
            }
        }
        $result .= '';

        $result .= $t . '}' . $n;

        return $result;
    }
}
