<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator;

use ReflectionClass;
use setasign\PhpStubGenerator\Formatter\ClassFormatter;
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
        $t = self::$tab;
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
                $aliases = $parser->getAliases($class->getName());
                foreach ($aliases as $fullName => $alias) {
                    $result .= $t . 'use ' . $fullName;
                    if ($alias !== substr($fullName, -strlen($alias))) {
                        $result .= ' as ' . $alias;
                    }

                    $result .= ';' . $n;
                }
                $result .= $n;

                $result .= (new ClassFormatter($parser, $class))->format();

                $result .= '}' . $n;
            }

        }

        return $result;
    }
}
