<?php

declare(strict_types=1);

namespace setasign\PhpStubGenerator\Tests\unit\Formatter;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use setasign\PhpStubGenerator\Formatter\ClassFormatter;
use setasign\PhpStubGenerator\PhpStubGenerator;

class ClassFormatterTest extends TestCase
{
    protected function createReflectionClassMock(): MockObject
    {
        return $this->getMockBuilder(\ReflectionClass::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getShortName',
                'getName',
                'getDocComment',
                'isInterface',
                'isTrait',
                'isAbstract',
                'isFinal',
                'getParentClass',
                'getInterfaces',
                'hasConstant',
                'getConstant',
                'isUserDefined',
                'implementsInterface'
            ])
            ->getMock();
    }

    /**
     * @throws \Throwable
     */
    public function testSimpleClass(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $reflectionClass = $this->createReflectionClassMock();
        $reflectionClass->method('getShortName')->willReturn('TestClass');
        $reflectionClass->method('getName')->willReturn('vendor\library\TestStuff\TestClass');
        $reflectionClass->method('getDocComment')->willReturn(false);
        $reflectionClass->method('isInterface')->willReturn(false);
        $reflectionClass->method('isTrait')->willReturn(false);
        $reflectionClass->method('isAbstract')->willReturn(false);
        $reflectionClass->method('isFinal')->willReturn(false);
        $reflectionClass->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $reflectionClass->method('getInterfaces')->willReturn([]);
        $reflectionClass->method('implementsInterface')->willReturn(false);
        $reflectionClass->method('hasConstant')->willReturn(false);
        $reflectionClass->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $reflectionClass->method('isUserDefined')->willReturn(true);

        /**
         * @var \ReflectionClass $reflectionClass
         */

        $formatter = new ClassFormatter($reflectionClass);
        $expectedResult = $t . 'class TestClass' . $n . $t . '{' . $n . $t . '}' . $n;
        $this->assertSame($expectedResult, $formatter->format(true));
    }

    /**
     * @throws \Throwable
     */
    public function testSimpleInterface(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $reflectionClass = $this->createReflectionClassMock();
        $reflectionClass->method('getShortName')->willReturn('TestInterface');
        $reflectionClass->method('getName')->willReturn('vendor\library\TestStuff\TestInterface');
        $reflectionClass->method('getDocComment')->willReturn(false);
        $reflectionClass->method('isInterface')->willReturn(true);
        $reflectionClass->method('isTrait')->willReturn(false);
        $reflectionClass->method('isAbstract')->willReturn(false);
        $reflectionClass->method('isFinal')->willReturn(false);
        $reflectionClass->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $reflectionClass->method('getInterfaces')->willReturn([]);
        $reflectionClass->method('implementsInterface')->willReturn(false);
        $reflectionClass->method('hasConstant')->willReturn(false);
        $reflectionClass->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $reflectionClass->method('isUserDefined')->willReturn(true);

        /**
         * @var \ReflectionClass $reflectionClass
         */

        $formatter = new ClassFormatter($reflectionClass);
        $expectedResult = $t . 'interface TestInterface' . $n . $t . '{' . $n . $t . '}' . $n;
        $this->assertSame($expectedResult, $formatter->format(true));
    }

    /**
     * @throws \Throwable
     */
    public function testSimpleTrait(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $reflectionClass = $this->createReflectionClassMock();
        $reflectionClass->method('getShortName')->willReturn('TestTrait');
        $reflectionClass->method('getName')->willReturn('vendor\library\TestStuff\TestTrait');
        $reflectionClass->method('getDocComment')->willReturn(false);
        $reflectionClass->method('isInterface')->willReturn(false);
        $reflectionClass->method('isTrait')->willReturn(true);
        $reflectionClass->method('isAbstract')->willReturn(false);
        $reflectionClass->method('isFinal')->willReturn(false);
        $reflectionClass->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $reflectionClass->method('getInterfaces')->willReturn([]);
        $reflectionClass->method('implementsInterface')->willReturn(false);
        $reflectionClass->method('hasConstant')->willReturn(false);
        $reflectionClass->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $reflectionClass->method('isUserDefined')->willReturn(true);

        /**
         * @var \ReflectionClass $reflectionClass
         */

        $formatter = new ClassFormatter($reflectionClass);
        $expectedResult = $t . 'trait TestTrait' . $n . $t . '{' . $n . $t . '}' . $n;
        $this->assertSame($expectedResult, $formatter->format(true));
    }

    /**
     * @throws \Throwable
     */
    public function testSimpleAbstract(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $reflectionClass = $this->createReflectionClassMock();
        $reflectionClass->method('getShortName')->willReturn('TestAbstract');
        $reflectionClass->method('getName')->willReturn('vendor\library\TestStuff\TestAbstract');
        $reflectionClass->method('getDocComment')->willReturn(false);
        $reflectionClass->method('isInterface')->willReturn(false);
        $reflectionClass->method('isTrait')->willReturn(false);
        $reflectionClass->method('isAbstract')->willReturn(true);
        $reflectionClass->method('isFinal')->willReturn(false);
        $reflectionClass->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $reflectionClass->method('getInterfaces')->willReturn([]);
        $reflectionClass->method('implementsInterface')->willReturn(false);
        $reflectionClass->method('hasConstant')->willReturn(false);
        $reflectionClass->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $reflectionClass->method('isUserDefined')->willReturn(true);

        /**
         * @var \ReflectionClass $reflectionClass
         */

        $formatter = new ClassFormatter($reflectionClass);
        $expectedResult = $t . 'abstract class TestAbstract' . $n . $t . '{' . $n . $t . '}' . $n;
        $this->assertSame($expectedResult, $formatter->format(true));
    }

    /**
     * @throws \Throwable
     */
    public function testSimpleFinal(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $reflectionClass = $this->createReflectionClassMock();
        $reflectionClass->method('getShortName')->willReturn('TestFinal');
        $reflectionClass->method('getName')->willReturn('vendor\library\TestStuff\TestFinal');
        $reflectionClass->method('getDocComment')->willReturn(false);
        $reflectionClass->method('isInterface')->willReturn(false);
        $reflectionClass->method('isTrait')->willReturn(false);
        $reflectionClass->method('isAbstract')->willReturn(false);
        $reflectionClass->method('isFinal')->willReturn(true);
        $reflectionClass->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $reflectionClass->method('getInterfaces')->willReturn([]);
        $reflectionClass->method('implementsInterface')->willReturn(false);
        $reflectionClass->method('hasConstant')->willReturn(false);
        $reflectionClass->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $reflectionClass->method('isUserDefined')->willReturn(true);

        /**
         * @var \ReflectionClass $reflectionClass
         */

        $formatter = new ClassFormatter($reflectionClass);
        $expectedResult = $t . 'final class TestFinal' . $n . $t . '{' . $n . $t . '}' . $n;
        $this->assertSame($expectedResult, $formatter->format(true));
    }

    /**
     * @throws \Throwable
     */
    public function testComplexClass(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $interfaceFromParent = $this->createReflectionClassMock();
        $interfaceFromParent->method('getShortName')->willReturn('AnotherInterface');
        $interfaceFromParent->method('getName')->willReturn('vendor\library\TestStuff\AnotherInterface');
        $interfaceFromParent->method('getDocComment')->willReturn(false);
        $interfaceFromParent->method('isInterface')->willReturn(true);
        $interfaceFromParent->method('isTrait')->willReturn(false);
        $interfaceFromParent->method('isAbstract')->willReturn(false);
        $interfaceFromParent->method('isFinal')->willReturn(false);
        $interfaceFromParent->method('implementsInterface')->willReturn(false);
        $interfaceFromParent->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $interfaceFromParent->method('getInterfaces')->willReturn([]);
        $interfaceFromParent->method('hasConstant')->willReturn(false);
        $interfaceFromParent->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $interfaceFromParent->method('isUserDefined')->willReturn(true);

        $parent = $this->createReflectionClassMock();
        $parent->method('getShortName')->willReturn('AnotherClass');
        $parent->method('getName')->willReturn('vendor\library\TestStuff\AnotherClass');
        $parent->method('getDocComment')->willReturn(false);
        $parent->method('isInterface')->willReturn(false);
        $parent->method('isTrait')->willReturn(false);
        $parent->method('isAbstract')->willReturn(false);
        $parent->method('isFinal')->willReturn(false);
        $parent->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $parent->method('getInterfaces')->willReturn([$interfaceFromParent]);
        $parent->method('implementsInterface')->willReturnCallback(function ($interface) {
            return $interface === 'vendor\library\TestStuff\AnotherInterface';
        });
        $parent->method('hasConstant')->willReturn(false);
        $parent->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $parent->method('isUserDefined')->willReturn(true);

        $interfaceFromInterface = $this->createReflectionClassMock();
        $interfaceFromInterface->method('getShortName')->willReturn('SomeOtherInterface');
        $interfaceFromInterface->method('getName')->willReturn('vendor\library\TestStuff\SomeOtherInterface');
        $interfaceFromInterface->method('getDocComment')->willReturn(false);
        $interfaceFromInterface->method('isInterface')->willReturn(true);
        $interfaceFromInterface->method('isTrait')->willReturn(false);
        $interfaceFromInterface->method('isAbstract')->willReturn(false);
        $interfaceFromInterface->method('isFinal')->willReturn(false);
        $interfaceFromInterface->method('implementsInterface')->willReturn(false);
        $interfaceFromInterface->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $interfaceFromInterface->method('getInterfaces')->willReturn([]);
        $interfaceFromInterface->method('hasConstant')->willReturn(false);
        $interfaceFromInterface->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $interfaceFromInterface->method('isUserDefined')->willReturn(true);

        $interfaceA = $this->createReflectionClassMock();
        $interfaceA->method('getShortName')->willReturn('InterfaceA');
        $interfaceA->method('getName')->willReturn('vendor\library\TestStuff\InterfaceA');
        $interfaceA->method('getDocComment')->willReturn(false);
        $interfaceA->method('isInterface')->willReturn(true);
        $interfaceA->method('isTrait')->willReturn(false);
        $interfaceA->method('isAbstract')->willReturn(false);
        $interfaceA->method('isFinal')->willReturn(false);
        $interfaceA->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $interfaceA->method('getInterfaces')->willReturn([$interfaceFromInterface]);
        $interfaceA->method('implementsInterface')->willReturnCallback(function ($interface) {
            return $interface === 'vendor\library\TestStuff\SomeOtherInterface';
        });
        $interfaceA->method('hasConstant')->willReturn(false);
        $interfaceA->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $interfaceA->method('isUserDefined')->willReturn(true);

        $interfaceB = $this->createReflectionClassMock();
        $interfaceB->method('getShortName')->willReturn('InterfaceB');
        $interfaceB->method('getName')->willReturn('vendor\library\TestStuff\InterfaceB');
        $interfaceB->method('getDocComment')->willReturn(false);
        $interfaceB->method('isInterface')->willReturn(true);
        $interfaceB->method('isTrait')->willReturn(false);
        $interfaceB->method('isAbstract')->willReturn(false);
        $interfaceB->method('isFinal')->willReturn(false);
        $interfaceB->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $interfaceB->method('getInterfaces')->willReturn([]);
        $interfaceB->method('implementsInterface')->willReturn(false);
        $interfaceB->method('hasConstant')->willReturn(false);
        $interfaceB->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $interfaceB->method('isUserDefined')->willReturn(true);

        $reflectionClass = $this->createReflectionClassMock();
        $reflectionClass->method('getShortName')->willReturn('TestClass');
        $reflectionClass->method('getName')->willReturn('vendor\library\TestStuff\TestClass');
        $reflectionClass->method('getDocComment')->willReturn(<<<EOT
/**
 * This is a cool test document!
 *
 * Just for test.
 *
 * @author Me
 * @copyright Nobody
 */
EOT
        );
        $reflectionClass->method('isInterface')->willReturn(false);
        $reflectionClass->method('isTrait')->willReturn(false);
        $reflectionClass->method('isAbstract')->willReturn(false);
        $reflectionClass->method('isFinal')->willReturn(false);
        $reflectionClass->method('getParentClass')->willReturn($parent);
        $reflectionClass->method('getInterfaces')->willReturn([
            $interfaceA, $interfaceB, $interfaceFromParent, $interfaceFromInterface
        ]);
        $reflectionClass->method('implementsInterface')->willReturn(false);
        $reflectionClass->method('hasConstant')->willReturn(false);
        $reflectionClass->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $reflectionClass->method('isUserDefined')->willReturn(true);

        /**
         * @var \ReflectionClass $reflectionClass
         */

        $formatter = new ClassFormatter($reflectionClass);
        $expectedResult = ''
            . $t . '/**' . $n
            . $t . ' * This is a cool test document!' . $n
            . $t . ' *' . $n
            . $t . ' * Just for test.' . $n
            . $t . ' *' . $n
            . $t . ' * @author Me' . $n
            . $t . ' * @copyright Nobody' . $n
            . $t . ' */' . $n
            . $t . 'class TestClass extends \vendor\library\TestStuff\AnotherClass implements '
                . '\vendor\library\TestStuff\InterfaceA, \vendor\library\TestStuff\InterfaceB' . $n
            . $t . '{' . $n
            . $t . '}' . $n;
        $this->assertSame($expectedResult, $formatter->format(true));
    }

    /**
     * @throws \Throwable
     */
    public function testComplexInterface(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $interfaceFromInterface = $this->createReflectionClassMock();
        $interfaceFromInterface->method('getShortName')->willReturn('SomeOtherInterface');
        $interfaceFromInterface->method('getName')->willReturn('vendor\library\TestStuff\SomeOtherInterface');
        $interfaceFromInterface->method('getDocComment')->willReturn(false);
        $interfaceFromInterface->method('isInterface')->willReturn(true);
        $interfaceFromInterface->method('isTrait')->willReturn(false);
        $interfaceFromInterface->method('isAbstract')->willReturn(false);
        $interfaceFromInterface->method('isFinal')->willReturn(false);
        $interfaceFromInterface->method('implementsInterface')->willReturn(false);
        $interfaceFromInterface->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $interfaceFromInterface->method('getInterfaces')->willReturn([]);
        $interfaceFromInterface->method('hasConstant')->willReturn(false);
        $interfaceFromInterface->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $interfaceFromInterface->method('isUserDefined')->willReturn(true);

        $interfaceA = $this->createReflectionClassMock();
        $interfaceA->method('getShortName')->willReturn('InterfaceA');
        $interfaceA->method('getName')->willReturn('vendor\library\TestStuff\InterfaceA');
        $interfaceA->method('getDocComment')->willReturn(false);
        $interfaceA->method('isInterface')->willReturn(true);
        $interfaceA->method('isTrait')->willReturn(false);
        $interfaceA->method('isAbstract')->willReturn(false);
        $interfaceA->method('isFinal')->willReturn(false);
        $interfaceA->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $interfaceA->method('getInterfaces')->willReturn([$interfaceFromInterface]);
        $interfaceA->method('implementsInterface')->willReturnCallback(function ($interface) {
            return $interface === 'vendor\library\TestStuff\SomeOtherInterface';
        });
        $interfaceA->method('hasConstant')->willReturn(false);
        $interfaceA->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $interfaceA->method('isUserDefined')->willReturn(true);

        $interfaceB = $this->createReflectionClassMock();
        $interfaceB->method('getShortName')->willReturn('InterfaceB');
        $interfaceB->method('getName')->willReturn('vendor\library\TestStuff\InterfaceB');
        $interfaceB->method('getDocComment')->willReturn(false);
        $interfaceB->method('isInterface')->willReturn(true);
        $interfaceB->method('isTrait')->willReturn(false);
        $interfaceB->method('isAbstract')->willReturn(false);
        $interfaceB->method('isFinal')->willReturn(false);
        $interfaceB->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $interfaceB->method('getInterfaces')->willReturn([]);
        $interfaceB->method('implementsInterface')->willReturn(false);
        $interfaceB->method('hasConstant')->willReturn(false);
        $interfaceB->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $interfaceB->method('isUserDefined')->willReturn(true);

        $reflectionClass = $this->createReflectionClassMock();
        $reflectionClass->method('getShortName')->willReturn('TestClass');
        $reflectionClass->method('getName')->willReturn('vendor\library\TestStuff\TestClass');
        $reflectionClass->method('getDocComment')->willReturn(<<<EOT
/**
 * This is a cool test document!
 *
 * Just for test.
 *
 * @author Me
 * @copyright Nobody
 */
EOT
        );
        $reflectionClass->method('isInterface')->willReturn(true);
        $reflectionClass->method('isTrait')->willReturn(false);
        $reflectionClass->method('isAbstract')->willReturn(false);
        $reflectionClass->method('isFinal')->willReturn(false);
        $reflectionClass->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $reflectionClass->method('getInterfaces')->willReturn([
            $interfaceA, $interfaceB, $interfaceFromInterface
        ]);
        $reflectionClass->method('implementsInterface')->willReturn(false);
        $reflectionClass->method('hasConstant')->willReturn(false);
        $reflectionClass->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $reflectionClass->method('isUserDefined')->willReturn(true);

        /**
         * @var \ReflectionClass $reflectionClass
         */

        $formatter = new ClassFormatter($reflectionClass);
        $expectedResult = ''
            . $t . '/**' . $n
            . $t . ' * This is a cool test document!' . $n
            . $t . ' *' . $n
            . $t . ' * Just for test.' . $n
            . $t . ' *' . $n
            . $t . ' * @author Me' . $n
            . $t . ' * @copyright Nobody' . $n
            . $t . ' */' . $n
            . $t . 'interface TestClass extends \vendor\library\TestStuff\InterfaceA, '
                . '\vendor\library\TestStuff\InterfaceB' . $n
            . $t . '{' . $n
            . $t . '}' . $n;
        $this->assertSame($expectedResult, $formatter->format(true));
    }
}
