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

    /**
     * TraitUseBlockFormatter constructor.
     *
     * @param ReflectionClass $reflectionClass
     */
    public function __construct(ReflectionClass $reflectionClass)
    {
        throw new \BadMethodCallException('TraitUseBlockFormatter isn\'t implemented!');
        $this->class = $reflectionClass;
    }

    /**
     * @return string
     */
    public function format(): string
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $traits = $this->class->getTraitNames();
        if (\count($traits) === 0) {
            return '';
        }
        var_dump($this->class->getName());
        var_dump($this->class->getTraitAliases());
        var_dump($traits);
//        die();
        // todo needed?
//        $traits = \array_filter($traits, function (ReflectionClass $trait) use ($traits) {
//            foreach ($traits as $compareTrait) {
//                /**
//                 * @var ReflectionClass $compareTrait
//                 */
//                if ($trait->($compareTrait->getName())) {
//                    return false;
//                }
//            }
//            return true;
//        });
        //$class->getTraitAliases();
        // todo use traits

        return '';
    }
}
