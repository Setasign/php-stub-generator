<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Tests\unit\Formatter;

use PHPUnit\Framework\TestCase;
use setasign\PhpStubGenerator\Formatter\ClassFormatter;
use setasign\PhpStubGenerator\Parser\ParserInterface;
use setasign\PhpStubGenerator\PhpStubGenerator;

class ClassFormatterTest extends TestCase
{
    protected function createParserInterfaceMock(): ParserInterface
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getMockBuilder(ParserInterface::class)
            ->getMockForAbstractClass();
    }

    protected function createReflectionClassMock(): \PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockBuilder(\ReflectionClass::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getShortName',
                'getName', // todo implement
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

    public function testSimpleClass()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $parser = $this->createParserInterfaceMock();

        $reflectionClass = $this->createReflectionClassMock();
        $reflectionClass->method('getDocComment')->willReturn(false);
        $reflectionClass->method('isInterface')->willReturn(false);
        $reflectionClass->method('isTrait')->willReturn(false);
        $reflectionClass->method('isAbstract')->willReturn(false);
        $reflectionClass->method('isFinal')->willReturn(false);
        $reflectionClass->method('getShortName')->willReturn('TestClass');
        $reflectionClass->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $reflectionClass->method('getInterfaces')->willReturn([]);
        $reflectionClass->method('implementsInterface')->willReturn(false);
        $reflectionClass->method('hasConstant')->willReturn(false);
        $reflectionClass->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $reflectionClass->method('isUserDefined')->willReturn(true);

        /**
         * @var \ReflectionClass $reflectionClass
         */

        $formatter = new ClassFormatter($parser, $reflectionClass);
        $expectedResult = $t . 'class TestClass' . $n . $t . '{' . $n . $t . '}' . $n;
        $this->assertSame($expectedResult, $formatter->format(true));
    }

    public function testSimpleInterface()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $parser = $this->createParserInterfaceMock();

        $reflectionClass = $this->createReflectionClassMock();
        $reflectionClass->method('getDocComment')->willReturn(false);
        $reflectionClass->method('isInterface')->willReturn(true);
        $reflectionClass->method('isTrait')->willReturn(false);
        $reflectionClass->method('isAbstract')->willReturn(false);
        $reflectionClass->method('isFinal')->willReturn(false);
        $reflectionClass->method('getShortName')->willReturn('TestInterface');
        $reflectionClass->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $reflectionClass->method('getInterfaces')->willReturn([]);
        $reflectionClass->method('implementsInterface')->willReturn(false);
        $reflectionClass->method('hasConstant')->willReturn(false);
        $reflectionClass->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $reflectionClass->method('isUserDefined')->willReturn(true);

        /**
         * @var \ReflectionClass $reflectionClass
         */

        $formatter = new ClassFormatter($parser, $reflectionClass);
        $expectedResult = $t . 'interface TestInterface' . $n . $t . '{' . $n . $t . '}' . $n;
        $this->assertSame($expectedResult, $formatter->format(true));
    }

    public function testSimpleTrait()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $parser = $this->createParserInterfaceMock();

        $reflectionClass = $this->createReflectionClassMock();
        $reflectionClass->method('getDocComment')->willReturn(false);
        $reflectionClass->method('isInterface')->willReturn(false);
        $reflectionClass->method('isTrait')->willReturn(true);
        $reflectionClass->method('isAbstract')->willReturn(false);
        $reflectionClass->method('isFinal')->willReturn(false);
        $reflectionClass->method('getShortName')->willReturn('TestTrait');
        $reflectionClass->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $reflectionClass->method('getInterfaces')->willReturn([]);
        $reflectionClass->method('implementsInterface')->willReturn(false);
        $reflectionClass->method('hasConstant')->willReturn(false);
        $reflectionClass->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $reflectionClass->method('isUserDefined')->willReturn(true);

        /**
         * @var \ReflectionClass $reflectionClass
         */

        $formatter = new ClassFormatter($parser, $reflectionClass);
        $expectedResult = $t . 'trait TestTrait' . $n . $t . '{' . $n . $t . '}' . $n;
        $this->assertSame($expectedResult, $formatter->format(true));
    }

    public function testSimpleAbstract()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $parser = $this->createParserInterfaceMock();

        $reflectionClass = $this->createReflectionClassMock();
        $reflectionClass->method('getDocComment')->willReturn(false);
        $reflectionClass->method('isInterface')->willReturn(false);
        $reflectionClass->method('isTrait')->willReturn(false);
        $reflectionClass->method('isAbstract')->willReturn(true);
        $reflectionClass->method('isFinal')->willReturn(false);
        $reflectionClass->method('getShortName')->willReturn('TestAbstract');
        $reflectionClass->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $reflectionClass->method('getInterfaces')->willReturn([]);
        $reflectionClass->method('implementsInterface')->willReturn(false);
        $reflectionClass->method('hasConstant')->willReturn(false);
        $reflectionClass->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $reflectionClass->method('isUserDefined')->willReturn(true);

        /**
         * @var \ReflectionClass $reflectionClass
         */

        $formatter = new ClassFormatter($parser, $reflectionClass);
        $expectedResult = $t . 'abstract class TestAbstract' . $n . $t . '{' . $n . $t . '}' . $n;
        $this->assertSame($expectedResult, $formatter->format(true));
    }

    public function testSimpleFinal()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $parser = $this->createParserInterfaceMock();

        $reflectionClass = $this->createReflectionClassMock();
        $reflectionClass->method('getDocComment')->willReturn(false);
        $reflectionClass->method('isInterface')->willReturn(false);
        $reflectionClass->method('isTrait')->willReturn(false);
        $reflectionClass->method('isAbstract')->willReturn(false);
        $reflectionClass->method('isFinal')->willReturn(true);
        $reflectionClass->method('getShortName')->willReturn('TestFinal');
        $reflectionClass->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $reflectionClass->method('getInterfaces')->willReturn([]);
        $reflectionClass->method('implementsInterface')->willReturn(false);
        $reflectionClass->method('hasConstant')->willReturn(false);
        $reflectionClass->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $reflectionClass->method('isUserDefined')->willReturn(true);

        /**
         * @var \ReflectionClass $reflectionClass
         */

        $formatter = new ClassFormatter($parser, $reflectionClass);
        $expectedResult = $t . 'final class TestFinal' . $n . $t . '{' . $n . $t . '}' . $n;
        $this->assertSame($expectedResult, $formatter->format(true));
    }

    public function testComplexClass()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $parser = $this->createParserInterfaceMock();

        $parent = $this->createReflectionClassMock();
        $parent->method('getDocComment')->willReturn(false);
        $parent->method('isInterface')->willReturn(false);
        $parent->method('isTrait')->willReturn(false);
        $parent->method('isAbstract')->willReturn(false);
        $parent->method('isFinal')->willReturn(false);
        $parent->method('getShortName')->willReturn('AnotherClass');
        $parent->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $parent->method('getInterfaces')->willReturn([]);
        $parent->method('implementsInterface')->willReturn(false);
        $parent->method('hasConstant')->willReturn(false);
        $parent->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $parent->method('isUserDefined')->willReturn(true);

        $interfaceA = $this->createReflectionClassMock();
        $interfaceA->method('getDocComment')->willReturn(false);
        $interfaceA->method('isInterface')->willReturn(true);
        $interfaceA->method('isTrait')->willReturn(false);
        $interfaceA->method('isAbstract')->willReturn(false);
        $interfaceA->method('isFinal')->willReturn(false);
        $interfaceA->method('getShortName')->willReturn('InterfaceA');
        $interfaceA->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $interfaceA->method('getInterfaces')->willReturn([]);
        $interfaceA->method('implementsInterface')->willReturn(false);
        $interfaceA->method('hasConstant')->willReturn(false);
        $interfaceA->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $interfaceA->method('isUserDefined')->willReturn(true);

        $interfaceB = $this->createReflectionClassMock();
        $interfaceB->method('getDocComment')->willReturn(false);
        $interfaceB->method('isInterface')->willReturn(true);
        $interfaceB->method('isTrait')->willReturn(false);
        $interfaceB->method('isAbstract')->willReturn(false);
        $interfaceB->method('isFinal')->willReturn(false);
        $interfaceB->method('getShortName')->willReturn('InterfaceB');
        $interfaceB->method('implementsInterface')->willReturn(false);
        $interfaceB->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $interfaceB->method('getInterfaces')->willReturn([]);
        $interfaceB->method('hasConstant')->willReturn(false);
        $interfaceB->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $interfaceB->method('isUserDefined')->willReturn(true);

        $reflectionClass = $this->createReflectionClassMock();
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
        $reflectionClass->method('getShortName')->willReturn('TestClass');
        $reflectionClass->method('getParentClass')->willReturn($parent);
        $reflectionClass->method('getInterfaces')->willReturn([$interfaceA, $interfaceB]);
        $reflectionClass->method('implementsInterface')->willReturn(false);
        $reflectionClass->method('hasConstant')->willReturn(false);
        $reflectionClass->method('getConstant')->willThrowException(new \Exception('Unknown constant'));
        $reflectionClass->method('isUserDefined')->willReturn(true);

        /**
         * @var \ReflectionClass $reflectionClass
         */

        $formatter = new ClassFormatter($parser, $reflectionClass);
        $expectedResult = ''
            . $t . '/**' . $n
            . $t . ' * This is a cool test document!' . $n
            . $t . ' *' . $n
            . $t . ' * Just for test.' . $n
            . $t . ' *' . $n
            . $t . ' * @author Me' . $n
            . $t . ' * @copyright Nobody' . $n
            . $t . ' */' . $n
            . $t . 'class TestClass extends AnotherClass implements InterfaceA, InterfaceB' . $n
            . $t . '{' . $n
            . $t . '}' . $n;
        $this->assertSame($expectedResult, $formatter->format(true));
    }
}
