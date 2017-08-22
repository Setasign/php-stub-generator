<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Tests\unit\Formatter;

use PHPUnit\Framework\TestCase;
use setasign\PhpStubGenerator\Formatter\ConstantFormatter;
use setasign\PhpStubGenerator\Parser\ParserInterface;
use setasign\PhpStubGenerator\Parser\ReflectionConst;
use setasign\PhpStubGenerator\PhpStubGenerator;

class ConstantFormatterTest extends TestCase
{
    protected function createParserInterfaceMock(): \PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockBuilder(ParserInterface::class)
            ->setMethods(['getConstantReflection'])
            ->getMockForAbstractClass();
    }

    protected function createReflectionConstMock(): \PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockBuilder(ReflectionConst::class)
            ->setMethods(['getDocComment'])
            ->getMockForAbstractClass();
    }

    protected function createReflectionClassMock(): \PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockBuilder(\ReflectionClass::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'hasConstant',
                'getConstant',
                'getParentClass',
                'getInterfaces'
            ])
            ->getMock();
    }

    public function testSimpleConstant()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $parser = $this->createParserInterfaceMock();
        $class = $this->createReflectionClassMock();
        $const = $this->createReflectionConstMock();

        $parser->method('getConstantReflection')->with($class, 'SUPER_CONSTANT')->willReturn($const);

        $class->method('hasConstant')->with('SUPER_CONSTANT')->willReturn(true);
        $class->method('getConstant')->with('SUPER_CONSTANT')->willReturn(true);
        $class->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $class->method('getInterfaces')->willReturn([]);

        $const->method('getDocComment')->willReturn(null);

        /**
         * @var ParserInterface $parser
         * @var \ReflectionClass $class
         */
        $expectedOutput = $t . $t . 'const SUPER_CONSTANT = true;' . $n . $n;
        $this->assertSame($expectedOutput, (new ConstantFormatter($parser, $class, 'SUPER_CONSTANT'))->format());
    }

    public function testWithInvalidConstant()
    {
        $parser = $this->createParserInterfaceMock();
        $class = $this->createReflectionClassMock();
        $const = $this->createReflectionConstMock();

        $parser->method('getConstantReflection')->with($class, 'SUPER_CONSTANT')->willReturn($const);

        $class->method('hasConstant')->with('SUPER_CONSTANT')->willReturn(false);
        $class->method('getConstant')->with('SUPER_CONSTANT')->willReturn(true);
        $class->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $class->method('getInterfaces')->willReturn([]);

        $const->method('getDocComment')->willReturn(null);

        /**
         * @var ParserInterface $parser
         * @var \ReflectionClass $class
         */
        $expectedOutput = '';
        $this->assertSame($expectedOutput, (new ConstantFormatter($parser, $class, 'SUPER_CONSTANT'))->format());
    }

    public function testConstantFromParent()
    {
        $parser = $this->createParserInterfaceMock();
        $class = $this->createReflectionClassMock();
        $const = $this->createReflectionConstMock();

        $parser->method('getConstantReflection')->with($class, 'SUPER_CONSTANT')->willReturn($const);

        $parentClass = $this->createReflectionClassMock();
        $parentClass->method('hasConstant')->with('SUPER_CONSTANT')->willReturn(true);
        $parentClass->method('getConstant')->with('SUPER_CONSTANT')->willReturn(true);

        $class->method('hasConstant')->with('SUPER_CONSTANT')->willReturn(true);
        $class->method('getConstant')->with('SUPER_CONSTANT')->willReturn(true);
        $class->method('getParentClass')->willReturn($parentClass);
        $class->method('getInterfaces')->willReturn([]);

        $const->method('getDocComment')->willReturn(null);

        /**
         * @var ParserInterface $parser
         * @var \ReflectionClass $class
         */
        $expectedOutput = '';
        $this->assertSame($expectedOutput, (new ConstantFormatter($parser, $class, 'SUPER_CONSTANT'))->format());
    }

    public function testConstantFromParentButChanged()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $parser = $this->createParserInterfaceMock();
        $class = $this->createReflectionClassMock();
        $const = $this->createReflectionConstMock();

        $parser->method('getConstantReflection')->with($class, 'SUPER_CONSTANT')->willReturn($const);

        $parentClass = $this->createReflectionClassMock();
        $parentClass->method('hasConstant')->with('SUPER_CONSTANT')->willReturn(true);
        $parentClass->method('getConstant')->with('SUPER_CONSTANT')->willReturn(true);

        $class->method('hasConstant')->with('SUPER_CONSTANT')->willReturn(true);
        $class->method('getConstant')->with('SUPER_CONSTANT')->willReturn(false);
        $class->method('getParentClass')->willReturn($parentClass);
        $class->method('getInterfaces')->willReturn([]);

        $const->method('getDocComment')->willReturn(null);

        /**
         * @var ParserInterface $parser
         * @var \ReflectionClass $class
         */
        $expectedOutput = $t . $t . 'const SUPER_CONSTANT = false;' . $n . $n;
        $this->assertSame($expectedOutput, (new ConstantFormatter($parser, $class, 'SUPER_CONSTANT'))->format());
    }

    public function testConstantFromInterface()
    {
        $parser = $this->createParserInterfaceMock();
        $class = $this->createReflectionClassMock();
        $const = $this->createReflectionConstMock();

        $parser->method('getConstantReflection')->with($class, 'SUPER_CONSTANT')->willReturn($const);

        $interface = $this->createReflectionClassMock();
        $interface->method('hasConstant')->with('SUPER_CONSTANT')->willReturn(true);
        $interface->method('getConstant')->with('SUPER_CONSTANT')->willReturn(true);

        $class->method('hasConstant')->with('SUPER_CONSTANT')->willReturn(true);
        $class->method('getConstant')->with('SUPER_CONSTANT')->willReturn(true);
        $class->method('getParentClass')->willReturn(new \Exception('Unknown parent'));
        $class->method('getInterfaces')->willReturn([$interface]);

        $const->method('getDocComment')->willReturn(null);

        /**
         * @var ParserInterface $parser
         * @var \ReflectionClass $class
         */
        $expectedOutput = '';
        $this->assertSame($expectedOutput, (new ConstantFormatter($parser, $class, 'SUPER_CONSTANT'))->format());
    }

    public function testConstantFromInterfaceButChanged()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $parser = $this->createParserInterfaceMock();
        $class = $this->createReflectionClassMock();
        $const = $this->createReflectionConstMock();

        $parser->method('getConstantReflection')->with($class, 'SUPER_CONSTANT')->willReturn($const);

        $interface = $this->createReflectionClassMock();
        $interface->method('hasConstant')->with('SUPER_CONSTANT')->willReturn(true);
        $interface->method('getConstant')->with('SUPER_CONSTANT')->willReturn(true);

        $class->method('hasConstant')->with('SUPER_CONSTANT')->willReturn(true);
        $class->method('getConstant')->with('SUPER_CONSTANT')->willReturn(false);
        $class->method('getParentClass')->willReturn(new \Exception('Unknown parent'));
        $class->method('getInterfaces')->willReturn([$interface]);

        $const->method('getDocComment')->willReturn(null);

        /**
         * @var ParserInterface $parser
         * @var \ReflectionClass $class
         */
        $expectedOutput = $t . $t . 'const SUPER_CONSTANT = false;' . $n . $n;
        $this->assertSame($expectedOutput, (new ConstantFormatter($parser, $class, 'SUPER_CONSTANT'))->format());
    }

    public function testConstantWithDocComment()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $parser = $this->createParserInterfaceMock();
        $class = $this->createReflectionClassMock();
        $const = $this->createReflectionConstMock();

        $parser->method('getConstantReflection')->with($class, 'SUPER_CONSTANT')->willReturn($const);

        $class->method('hasConstant')->with('SUPER_CONSTANT')->willReturn(true);
        $class->method('getConstant')->with('SUPER_CONSTANT')->willReturn(true);
        $class->method('getParentClass')->willThrowException(new \Exception('Unknown parent'));
        $class->method('getInterfaces')->willReturn([]);

        $const->method('getDocComment')->willReturn(<<<EOT
/**
 * This is just a cool test case!
 *
 * Just for test.
 *
 * @var bool
 */
EOT
        );

        /**
         * @var ParserInterface $parser
         * @var \ReflectionClass $class
         */
        $expectedOutput = $t . $t . '/**' . $n
            . $t . $t . ' * This is just a cool test case!' . $n
            . $t . $t . ' *' . $n
            . $t . $t . ' * Just for test.' . $n
            . $t . $t . ' *' . $n
            . $t . $t . ' * @var bool' . $n
            . $t . $t . ' */' . $n
            . $t . $t . 'const SUPER_CONSTANT = true;' . $n . $n;
        $this->assertSame($expectedOutput, (new ConstantFormatter($parser, $class, 'SUPER_CONSTANT'))->format());
    }
}
