<?php

declare(strict_types=1);

namespace setasign\PhpStubGenerator\Tests\unit\Formatter;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use setasign\PhpStubGenerator\Formatter\PropertyFormatter;
use setasign\PhpStubGenerator\PhpStubGenerator;

class PropertyFormatterTest extends TestCase
{
    protected function createReflectionPropertyMock(
        string $declaringClassName
    ): MockObject {
        $declaringClass = $this->getMockBuilder(\ReflectionClass::class)
            ->onlyMethods(['getName'])
            ->disableOriginalConstructor()
            ->getMock();
        $declaringClass->method('getName')->willReturn($declaringClassName);

        $property = $this->getMockBuilder(\ReflectionProperty::class)
            ->onlyMethods([
                'getName',
                'isDefault',
                'getDeclaringClass',
                'getDocComment',
                'isPublic',
                'isProtected',
                'isPrivate',
                'isStatic'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $property->method('getDeclaringClass')->willReturn($declaringClass);

        return $property;
    }

    /**
     * @throws \Throwable
     */
    public function testSimpleProperty(): void
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

        /**
         * @var \ReflectionProperty $property
         */
        $expectedOutput = $t . $t . 'public $test = 123;' . $n . $n;
        $this->assertSame($expectedOutput, (new PropertyFormatter('TestClass', $property, 123))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testProtectedProperty(): void
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

        /**
         * @var \ReflectionProperty $property
         */
        $expectedOutput = $t . $t . 'protected $test = 123;' . $n . $n;
        $this->assertSame($expectedOutput, (new PropertyFormatter('TestClass', $property, 123))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testPrivateProperty(): void
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

        /**
         * @var \ReflectionProperty $property
         */
        $expectedOutput = $t . $t . 'private $test = 123;' . $n . $n;
        $this->assertSame($expectedOutput, (new PropertyFormatter('TestClass', $property, 123))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testStaticProperty(): void
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

        /**
         * @var \ReflectionProperty $property
         */
        $expectedOutput = $t . $t . 'public static $test = 123;' . $n . $n;
        $this->assertSame($expectedOutput, (new PropertyFormatter('TestClass', $property, 123))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testIsNotDefaultProperty(): void
    {
        $property = $this->createReflectionPropertyMock('TestClass');

        $property->method('getName')->willReturn('test');
        $property->method('isDefault')->willReturn(false);
        $property->method('getDocComment')->willReturn(false);
        $property->method('isPublic')->willReturn(true);
        $property->method('isProtected')->willReturn(false);
        $property->method('isPrivate')->willReturn(false);
        $property->method('isStatic')->willReturn(false);

        /**
         * @var \ReflectionProperty $property
         */
        $expectedOutput = '';
        $this->assertSame($expectedOutput, (new PropertyFormatter('TestClass', $property, 123))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testPropertyWithoutValue(): void
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

        /**
         * @var \ReflectionProperty $property
         */
        $expectedOutput = $t . $t . 'public $test;' . $n . $n;
        $this->assertSame($expectedOutput, (new PropertyFormatter('TestClass', $property, null))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testPropertyFromParent(): void
    {
        $property = $this->createReflectionPropertyMock('AnotherClass');

        $property->method('getName')->willReturn('test');
        $property->method('isDefault')->willReturn(true);
        $property->method('getDocComment')->willReturn(false);
        $property->method('isPublic')->willReturn(true);
        $property->method('isProtected')->willReturn(false);
        $property->method('isPrivate')->willReturn(false);
        $property->method('isStatic')->willReturn(false);

        /**
         * @var \ReflectionProperty $property
         */
        $expectedOutput = '';
        $this->assertSame($expectedOutput, (new PropertyFormatter('TestClass', $property, 123))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testPropertyWithDoc(): void
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
        $this->assertSame($expectedOutput, (new PropertyFormatter('TestClass', $property, 123))->format());
    }
}
