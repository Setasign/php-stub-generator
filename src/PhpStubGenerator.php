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
     * End of line character(s).
     *
     * Doesn't change the used EOL character(s) of doc blocks.
     *
     * @var string
     */
    public static string $eol = "\n";

    /**
     * Tab character(s)
     *
     * @var string
     */
    public static string $tab = '    ';

    /**
     * If enabled all generated class constants get a visibility (the generated stubs require PHP >= 7.1).
     *
     * Within the cli tool can be set with the option "--addClassConstantsVisibility"
     *
     * @var bool
     */
    public static bool $addClassConstantsVisibility = false;

    /**
     * @var ReaderInterface[]
     */
    private array $sources = [];

    /**
     * @param string $name
     * @param ReaderInterface $reader
     */
    public function addSource(string $name, ReaderInterface $reader): void
    {
        $this->sources[$name] = $reader;
    }

    /**
     * @param string $name
     */
    public function removeSource(string $name): void
    {
        unset($this->sources[$name]);
    }

    /**
     * @return ParserInterface
     */
    public function getParser(): ParserInterface
    {
        return new GoaopParserReflectionParser($this->sources);
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public function generate(): string
    {
        $n = self::$eol;
        $result = '<?php' . $n
                . '/** @noinspection ALL */' . $n . $n
                . '// @codingStandardsIgnoreFile' . $n . $n;

        $parser = $this->getParser();
        $parser->parse();
        /**
         * @var ReflectionClass[] $classes
         */
        foreach ($parser->getClasses() as $namespace => $classes) {
            foreach ($classes as $class) {
                $isGlobalNamespace = $namespace === '';
                $result .= 'namespace' . (!$isGlobalNamespace ? ' ' . $namespace : '') . $n
                    . '{' . $n;
                $result .= $this->formatNamespaceAliases(
                    $parser->getAliases($class->getName(), ParserInterface::TYPE_CLASS)
                );
                $result .= $n
                    . (new ClassFormatter($class))->format()
                    . '}' . $n . $n;
            }
        }

        /**
         * @var ReflectionFunction[] $functions
         */
        foreach ($parser->getFunctions() as $namespace => $functions) {
            foreach ($functions as $function) {
                $isGlobalNamespace = ($namespace === '');
                $result .= 'namespace' . (!$isGlobalNamespace ? ' ' . $namespace : '') . $n
                    . '{' . $n;
                $result .= $this->formatNamespaceAliases(
                    $parser->getAliases($function->getName(), ParserInterface::TYPE_FUNCTION)
                );
                $result .= $n
                    . (new FunctionFormatter($function))->format()
                    . '}' . $n . $n;
            }
        }

        return $result;
    }

    /**
     * @param array $aliases
     * @return string
     */
    protected function formatNamespaceAliases(array $aliases): string
    {
        $n = self::$eol;
        $t = self::$tab;

        $result = '';
        foreach ($aliases as $fullName => $alias) {
            $alias = (string) $alias;

            $result .= $t . 'use ' . $fullName;
            if ($alias !== \substr($fullName, -\strlen($alias))) {
                $result .= ' as ' . $alias;
            }
            $result .= ';' . $n;
        }
        return $result;
    }
}
