<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Formatter;

use ReflectionClass;
use setasign\PhpStubGenerator\PhpStubGenerator;

class TraitUseBlockFormatter
{
    /**
     * @var ReflectionClass
     */
    private $class;

    public function __construct(ReflectionClass $reflectionClass)
    {
        $this->class = $reflectionClass;
    }

    public function format(): string
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        //$traits = $class->getTraits();
        // todo needed?
//                $traits = array_filter($traits, function (ReflectionClass $trait) use ($traits) {
//                    foreach ($traits as $compareTrait) {
//                        /**
//                         * @var ReflectionClass $compareTrait
//                         */
//                        if ($trait->($compareTrait->getName())) {
//                            return false;
//                        }
//                    }
//                    return true;
//                });
        //$class->getTraitAliases();
        // todo use traits

        return '';
    }
}
