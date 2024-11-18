<?php

declare(strict_types=1);

namespace setasign\PhpStubGenerator\Parser\GoaopParserReflection;

use Go\ParserReflection\LocatorInterface;

class ClassListLocator implements LocatorInterface
{
    private array $classMap;

    /**
     * ClassListLocator constructor.
     *
     * @param array $classMap Key = ClassName Value = File
     */
    public function __construct(array $classMap)
    {
        $this->classMap = $classMap;
    }

    public function locateClass($className)
    {
        if (\strpos($className, '\\') === 0) {
            $className = \ltrim($className, '\\');
        }

        if (!isset($this->classMap[$className])) {
            return false;
        }

        return $this->classMap[$className];
    }
}
