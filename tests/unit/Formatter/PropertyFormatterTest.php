<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Tests\unit\Formatter;

use PHPUnit\Framework\TestCase;
use setasign\PhpStubGenerator\Formatter\PropertyFormatter;
use setasign\PhpStubGenerator\PhpStubGenerator;

class PropertyFormatterTest extends TestCase
{
    protected function createReflectionPropertyMock(
        string $declaringClassName
    ): \PHPUnit_Framework_MockObject_MockObject {
        $declaringClass = $this->getMockBuilder(\ReflectionClass::class)
            ->setMethods(['getName'])
            ->disableOriginalConstructor()
            ->getMock();
        $declaringClass->method('getName')->willReturn($declaringClassName);

        $property = $this->getMockBuilder(\ReflectionProperty::class)
            ->setMethods([
                'getName',
                'isDefault',
                'getDeclaringClass',
                'getDocComment',
                'isPublic',
                'isProtected',
                'isPrivate',
                'isStatic',
                'getValue'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $property->method('getDeclaringClass')->willReturn($declaringClass);

        return $property;
    }

    public function testSimpleProperty()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $property = $this->createReflectionPropertyMock('TestClass');

        $property->method('getName')->willReturn('test');
        $property->method('isDefault')->willReturn(true);
        $property->method('getDocComment')->willReturn(false);
        $property->method('isPublic')->willReturn(true);
        $property->method('isProtected')->willReturn(false);
        $property->method('isPrivate')->willReturn(false);
        $property->method('isStatic')->willReturn(false);
        $property->method('getValue')->willReturn(123);

        /**
         * @var \ReflectionProperty $property
         */
        $expectedOutput = $t . $t . 'public $test = 123;' . $n . $n;
        $this->assertSame($expectedOutput, (new PropertyFormatter('TestClass', $property))->format());
    }

    public function testProtectedProperty()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $property = $this->createReflectionPropertyMock('TestClass');

        $property->method('getName')->willReturn('test');
        $property->method('isDefault')->willReturn(true);
        $property->method('getDocComment')->willReturn(false);
        $property->method('isPublic')->willReturn(false);
        $property->method('isProtected')->willReturn(true);
        $property->method('isPrivate')->willReturn(false);
        $property->method('isStatic')->willReturn(false);
        $property->method('getValue')->willReturn(123);

        /**
         * @var \ReflectionProperty $property
         */
        $expectedOutput = $t . $t . 'protected $test = 123;' . $n . $n;
        $this->assertSame($expectedOutput, (new PropertyFormatter('TestClass', $property))->format());
    }

    public function testPrivateProperty()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $property = $this->createReflectionPropertyMock('TestClass');

        $property->method('getName')->willReturn('test');
        $property->method('isDefault')->willReturn(true);
        $property->method('getDocComment')->willReturn(false);
        $property->method('isPublic')->willReturn(false);
        $property->method('isProtected')->willReturn(false);
        $property->method('isPrivate')->willReturn(true);
        $property->method('isStatic')->willReturn(false);
        $property->method('getValue')->willReturn(123);

        /**
         * @var \ReflectionProperty $property
         */
        $expectedOutput = $t . $t . 'private $test = 123;' . $n . $n;
        $this->assertSame($expectedOutput, (new PropertyFormatter('TestClass', $property))->format());
    }

    public function testStaticProperty()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $property = $this->createReflectionPropertyMock('TestClass');

        $property->method('getName')->willReturn('test');
        $property->method('isDefault')->willReturn(true);
        $property->method('getDocComment')->willReturn(false);
        $property->method('isPublic')->willReturn(true);
        $property->method('isProtected')->willReturn(false);
        $property->method('isPrivate')->willReturn(false);
        $property->method('isStatic')->willReturn(true);
        $property->method('getValue')->willReturn(123);

        /**
         * @var \ReflectionProperty $property
         */
        $expectedOutput = $t . $t . 'public static $test = 123;' . $n . $n;
        $this->assertSame($expectedOutput, (new PropertyFormatter('TestClass', $property))->format());
    }

    public function testIsNotDefaultProperty()
    {
        $property = $this->createReflectionPropertyMock('TestClass');

        $property->method('getName')->willReturn('test');
        $property->method('isDefault')->willReturn(false);
        $property->method('getDocComment')->willReturn(false);
        $property->method('isPublic')->willReturn(true);
        $property->method('isProtected')->willReturn(false);
        $property->method('isPrivate')->willReturn(false);
        $property->method('isStatic')->willReturn(false);
        $property->method('getValue')->willReturn(123);

        /**
         * @var \ReflectionProperty $property
         */
        $expectedOutput = '';
        $this->assertSame($expectedOutput, (new PropertyFormatter('TestClass', $property))->format());
    }

    public function testPropertyWithoutValue()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $property = $this->createReflectionPropertyMock('TestClass');

        $property->method('getName')->willReturn('test');
        $property->method('isDefault')->willReturn(true);
        $property->method('getDocComment')->willReturn(false);
        $property->method('isPublic')->willReturn(true);
        $property->method('isProtected')->willReturn(false);
        $property->method('isPrivate')->willReturn(false);
        $property->method('isStatic')->willReturn(false);
        $property->method('getValue')->willReturn(null);

        /**
         * @var \ReflectionProperty $property
         */
        $expectedOutput = $t . $t . 'public $test;' . $n . $n;
        $this->assertSame($expectedOutput, (new PropertyFormatter('TestClass', $property))->format());
    }

    public function testPropertyFromParent()
    {
        $property = $this->createReflectionPropertyMock('AnotherClass');

        $property->method('getName')->willReturn('test');
        $property->method('isDefault')->willReturn(true);
        $property->method('getDocComment')->willReturn(false);
        $property->method('isPublic')->willReturn(true);
        $property->method('isProtected')->willReturn(false);
        $property->method('isPrivate')->willReturn(false);
        $property->method('isStatic')->willReturn(false);
        $property->method('getValue')->willReturn(123);

        /**
         * @var \ReflectionProperty $property
         */
        $expectedOutput = '';
        $this->assertSame($expectedOutput, (new PropertyFormatter('TestClass', $property))->format());
    }

    public function testPropertyWithDoc()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $property = $this->createReflectionPropertyMock('TestClass');

        $property->method('getName')->willReturn('test');
        $property->method('isDefault')->willReturn(true);
        $property->method('getDocComment')->willReturn(<<<EOT
/**
 * This is a cool test document!
 *
 * Just for test.
 *
 * @var int
 */
EOT
        );
        $property->method('isPublic')->willReturn(true);
        $property->method('isProtected')->willReturn(false);
        $property->method('isPrivate')->willReturn(false);
        $property->method('isStatic')->willReturn(false);
        $property->method('getValue')->willReturn(123);

        /**
         * @var \ReflectionProperty $property
         */
        $expectedOutput = ''
            . $t . $t . '/**' . $n
            . $t . $t . ' * This is a cool test document!' . $n
            . $t . $t . ' *' . $n
            . $t . $t . ' * Just for test.' . $n
            . $t . $t . ' *' . $n
            . $t . $t . ' * @var int' . $n
            . $t . $t . ' */' . $n
            . $t . $t . 'public $test = 123;' . $n . $n;
        $this->assertSame($expectedOutput, (new PropertyFormatter('TestClass', $property))->format());
    }
}
