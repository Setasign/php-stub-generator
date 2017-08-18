<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator;

use ReflectionClass;
use Go\ParserReflection\ReflectionEngine;
use Go\ParserReflection\ReflectionFile;
use Go\ParserReflection\ReflectionFileNamespace;
use setasign\PhpStubGenerator\Helper\StringHelper;
use setasign\PhpStubGenerator\Parser\ClassListLocator;
use setasign\PhpStubGenerator\Parser\ReflectionConst;
use setasign\PhpStubGenerator\Reader\ReaderInterface;

class PhpStubGenerator
{
    /**
     * @var ReaderInterface[]
     */
    private $sources = [];

    /**
     * @var string
     */
    private $eol = "\n";

    /**
     * @var string
     */
    private $tab = '    ';

    public function addSource(string $name, ReaderInterface $reader): void
    {
        $this->sources[$name] = $reader;
    }

    public function removeSource(string $name): void
    {
        unset($this->sources[$name]);
    }

    public function setEOL(string $eol): void
    {
        $this->eol = $eol;
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function generate(): string
    {
        $n = $this->eol;
        $t = $this->tab;
        $result = '<?php' . $n . $n;

        $namespaces = $this->resolveNamespaces();

        $paths = array_map(function (array $classes) {
            return array_map(function (ReflectionClass $reflectionClass) {
                return $reflectionClass->getFileName();
            }, $classes);
        }, $namespaces);

        $classMap = array_merge(...array_values($paths));
        $locator = new ClassListLocator($classMap);
        ReflectionEngine::init($locator);

        foreach ($namespaces as $namespace => $classes) {
            /**
             * @var ReflectionClass[] $classes
             */

            $isGlobalNamespace = $namespace === '';
            $result .= 'namespace' . (!$isGlobalNamespace ? ' ' . $namespace : '') . $n
                     . '{' . $n;

            foreach ($classes as $class) {
                $result .= StringHelper::indentBlock($class->getDocComment(), 1, $t) . $n
                         . $t;

                if ($class->isInterface()) {
                    $result .= 'interface ';
                } elseif ($class->isTrait()) {
                    $result .= 'trait ';
                } else {
                    if ($class->isAbstract()) {
                        $result .= 'abstract ';
                    } elseif ($class->isFinal()) {
                        $result .= 'final ';
                    }

                    $result .= 'class ';
                }
                $result .= $class->getShortName();
                $parentClass = null;
                try {
                    $parentClass = $class->getParentClass();
                } catch (\Exception $e) {
                }
                if ($parentClass instanceof ReflectionClass) {
                    $result .= ' extends \\' . $parentClass->getName();
                }

                $interfaces = $class->getInterfaces();
                // remove interfaces from parent class if there is a parent class
                if ($parentClass instanceof ReflectionClass) {
                    $interfaces = array_filter($interfaces, function (ReflectionClass $interface) use ($parentClass) {
                        return !$parentClass->implementsInterface($interface->getName());
                    });
                }

                // remove sub interfaces of other interfaces
                $interfaces = array_filter($interfaces, function (ReflectionClass $interface) use ($interfaces) {
                    foreach ($interfaces as $compareInterface) {
                        /**
                         * @var ReflectionClass $compareInterface
                         */
                        if ($interface->implementsInterface($compareInterface->getName())) {
                            return false;
                        }
                    }
                    return true;
                });

                $interfaces = array_map(function (ReflectionClass $interface) {
                    return $interface->getName();
                }, $interfaces);

                if (count($interfaces) > 0) {
                    if ($class->isInterface()) {
                        $result .= ' extends ';
                    } else {
                        $result .= ' implements ';
                    }

                    $interfaces = array_map(function ($interface) {
                        return '\\' . $interface;
                    }, $interfaces);

                    $result .= implode(', ', $interfaces);
                }

                $result .= $n . $t . '{' . $n;

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

                foreach ($class->getConstants() as $constantName => $constantValue) {
                    if ($parentClass instanceof ReflectionClass && $parentClass->hasConstant($constantName)
                        && $parentClass->getConstant($constantName) === $constantValue
                    ) {
                        continue;
                    }

                    $reflectionConst = null;
                    try {
                        $reflectionConst = new ReflectionConst($class, $constantName);
                    } catch (\ReflectionException $e) {
                    }

                    if ($reflectionConst instanceof ReflectionConst) {
                        $result .= StringHelper::indentBlock((string) $reflectionConst->getDocComment(), 2, $t) . $n;
                    }
                    $result .= $t . $t . 'const ' . $constantName . ' = ' . $constantValue . ';' . $n . $n;
                }
                // todo class content

                $result .= $t . '}' . $n;
            }

            $result .= '}' . $n;
        }

        return $result;
    }

    /**
     * @return ReflectionClass[][]
     */
    protected function resolveNamespaces(): array
    {
        $files = array_map(function (ReaderInterface $source) {
            return array_map(function (string $file) {
                return new ReflectionFile($file);
            }, $source->getFiles());
        }, $this->sources);
        $files = array_merge(...array_values($files));

        $fileNamespaces = array_map(function (ReflectionFile $file) {
            return $file->getFileNamespaces();
        }, $files);

        /**
         * @var array $namespaces
         */
        $namespaces = array_reduce($fileNamespaces, function (array $carry, array $fileNamespaces) {
            foreach ($fileNamespaces as $fileNamespace) {
                $carry[$fileNamespace->getName()][] = $fileNamespace->getClasses();
            }
            return $carry;
        }, []);

        foreach ($namespaces as $name => $classes) {
            $namespaces[$name] = array_merge(...$classes);
        }

        return $namespaces;
    }
}
