<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator;

use ReflectionClass;
use ReflectionFunction;
use setasign\PhpStubGenerator\Formatter\ClassFormatter;
use setasign\PhpStubGenerator\Formatter\FunctionFormatter;
use setasign\PhpStubGenerator\Parser\GoaopParserReflection\GoaopParserReflectionParser;
use setasign\PhpStubGenerator\Parser\ParserInterface;
use setasign\PhpStubGenerator\Reader\ReaderInterface;

class PhpStubGenerator
{
    /**
     * @var string
     */
    public static $eol = "\n";

    /**
     * @var string
     */
    public static $tab = '    ';

    /**
     * @var ReaderInterface[]
     */
    private $sources = [];

    public function addSource(string $name, ReaderInterface $reader): void
    {
        $this->sources[$name] = $reader;
    }

    public function removeSource(string $name): void
    {
        unset($this->sources[$name]);
    }

    public function getParser(): ParserInterface
    {
        return new GoaopParserReflectionParser($this->sources);
    }

    public function generate(): string
    {
        $n = self::$eol;
        $result = '<?php' . $n
                . '// @codingStandardsIgnoreFile' . $n . $n;

        $parser = $this->getParser();
        $parser->parse();
        foreach ($parser->getClasses() as $namespace => $classes) {
            /**
             * @var ReflectionClass[] $classes
             */

            foreach ($classes as $class) {
                $isGlobalNamespace = $namespace === '';
                $result .= 'namespace' . (!$isGlobalNamespace ? ' ' . $namespace : '') . $n
                    . '{' . $n;
                $result .= $this->formatAliases(
                    $parser->getAliases($class->getName(), ParserInterface::TYPE_CLASS)
                );
                $result .= $n
                    . (new ClassFormatter($parser, $class))->format()
                    . '}' . $n . $n;
            }
        }

        foreach ($parser->getFunctions() as $namespace => $functions) {
            /**
             * @var ReflectionFunction[] $functions
             */
            foreach ($functions as $function) {
                $isGlobalNamespace = $namespace === '';
                $result .= 'namespace' . (!$isGlobalNamespace ? ' ' . $namespace : '') . $n
                    . '{' . $n;
                $result .= $this->formatAliases(
                    $parser->getAliases($function->getName(), ParserInterface::TYPE_FUNCTION)
                );
                $result .= $n
                    . (new FunctionFormatter($function))->format()
                    . '}' . $n . $n;
            }
        }

        return $result;
    }

    protected function formatAliases(array $aliases): string
    {
        $n = self::$eol;
        $t = self::$tab;

        $result = '';
        foreach ($aliases as $fullName => $alias) {
            $result .= $t . 'use ' . $fullName;
            if ($alias !== substr($fullName, -strlen($alias))) {
                $result .= ' as ' . $alias;
            }
            $result .= ';' . $n;
        }
        return $result;
    }
}
