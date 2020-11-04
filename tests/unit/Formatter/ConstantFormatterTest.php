<?php

declare(strict_types=1);

namespace setasign\PhpStubGenerator\Tests\unit\Formatter;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use setasign\PhpStubGenerator\Formatter\ConstantFormatter;
use setasign\PhpStubGenerator\PhpStubGenerator;

class ConstantFormatterTest extends TestCase
{
    protected function createReflectionConstMock(): MockObject
    {
        return $this->getMockBuilder(\ReflectionClassConstant::class)
            ->onlyMethods([
                'getDocComment',
                'getName',
                'getValue',
                'getDeclaringClass',
                'isPublic',
                'isProtected',
                'isPrivate'
            ])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function createReflectionClassMock(): MockObject
    {
        return $this->getMockBuilder(\ReflectionClass::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getName',
                'getReflectionConstant',
            ])
            ->getMock();
    }

    /**
     * @throws \Throwable
     */
    public function testSimpleConstant(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $class = $this->createReflectionClassMock();
        $const = $this->createReflectionConstMock();

        $class->method('getName')->willReturn('TestClass');
        $class->method('getReflectionConstant')->with('SUPER_CONSTANT')->willReturn($const);

        $const->method('getDocComment')->willReturn(false);
        $const->method('getName')->willReturn('SUPER_CONSTANT');
        $const->method('getValue')->willReturn(true);
        $const->method('getDeclaringClass')->willReturn($class);
        $const->method('isPublic')->willReturn(true);
        $const->method('isProtected')->willReturn(false);
        $const->method('isPrivate')->willReturn(false);

        /**
         * @var \ReflectionClass $class
         * @var \ReflectionClassConstant $const
         */
        $expectedOutput = $t . $t . 'const SUPER_CONSTANT = true;' . $n . $n;
        $this->assertSame($expectedOutput, (new ConstantFormatter('TestClass', $const))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testConstantFromParent(): void
    {
        $const = $this->createReflectionConstMock();

        $parentClass = $this->createReflectionClassMock();
        $parentClass->method('getName')->willReturn('ParentClass');
        $parentClass->method('getReflectionConstant')->with('SUPER_CONSTANT')->willReturn($const);

        $const->method('getDeclaringClass')->willReturn($parentClass);

        /**
         * @var \ReflectionClassConstant $const
         */
        $expectedOutput = '';
        $this->assertSame($expectedOutput, (new ConstantFormatter('ImplementingClass', $const))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testConstantWithDocComment(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $class = $this->createReflectionClassMock();
        $const = $this->createReflectionConstMock();

        $class->method('getName')->willReturn('TestClass');
        $class->method('getReflectionConstant')->with('SUPER_CONSTANT')->willReturn($const);

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
        $const->method('getName')->willReturn('SUPER_CONSTANT');
        $const->method('getValue')->willReturn(true);
        $const->method('getDeclaringClass')->willReturn($class);
        $const->method('isPublic')->willReturn(true);
        $const->method('isProtected')->willReturn(false);
        $const->method('isPrivate')->willReturn(false);

        /**
         * @var \ReflectionClassConstant $const
         */
        $expectedOutput = $t . $t . '/**' . $n
            . $t . $t . ' * This is just a cool test case!' . $n
            . $t . $t . ' *' . $n
            . $t . $t . ' * Just for test.' . $n
            . $t . $t . ' *' . $n
            . $t . $t . ' * @var bool' . $n
            . $t . $t . ' */' . $n
            . $t . $t . 'const SUPER_CONSTANT = true;' . $n . $n;
        $this->assertSame($expectedOutput, (new ConstantFormatter('TestClass', $const))->format());
    }

    public function testPublicConstant(): void
    {
        PhpStubGenerator::$addClassConstantsVisibility = true;
        \defer($_, function () {
            PhpStubGenerator::$addClassConstantsVisibility = false;
        });

        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $class = $this->createReflectionClassMock();
        $const = $this->createReflectionConstMock();

        $class->method('getName')->willReturn('TestClass');
        $class->method('getReflectionConstant')->with('SUPER_CONSTANT')->willReturn($const);

        $const->method('getDocComment')->willReturn(false);
        $const->method('getName')->willReturn('SUPER_CONSTANT');
        $const->method('getValue')->willReturn(true);
        $const->method('getDeclaringClass')->willReturn($class);
        $const->method('isPublic')->willReturn(true);
        $const->method('isProtected')->willReturn(false);
        $const->method('isPrivate')->willReturn(false);

        /**
         * @var \ReflectionClassConstant $const
         */
        $expectedOutput = $t . $t . 'public const SUPER_CONSTANT = true;' . $n . $n;
        $this->assertSame($expectedOutput, (new ConstantFormatter('TestClass', $const))->format());
    }

    public function testProtectedConstant(): void
    {
        PhpStubGenerator::$addClassConstantsVisibility = true;
        \defer($_, function () {
            PhpStubGenerator::$addClassConstantsVisibility = false;
        });

        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $class = $this->createReflectionClassMock();
        $const = $this->createReflectionConstMock();

        $class->method('getName')->willReturn('TestClass');
        $class->method('getReflectionConstant')->with('SUPER_CONSTANT')->willReturn($const);

        $const->method('getDocComment')->willReturn(false);
        $const->method('getName')->willReturn('SUPER_CONSTANT');
        $const->method('getValue')->willReturn(true);
        $const->method('getDeclaringClass')->willReturn($class);
        $const->method('isPublic')->willReturn(false);
        $const->method('isProtected')->willReturn(true);
        $const->method('isPrivate')->willReturn(false);

        /**
         * @var \ReflectionClassConstant $const
         */
        $expectedOutput = $t . $t . 'protected const SUPER_CONSTANT = true;' . $n . $n;
        $this->assertSame($expectedOutput, (new ConstantFormatter('TestClass', $const))->format());
    }

    public function testPrivateConstant(): void
    {
        PhpStubGenerator::$addClassConstantsVisibility = true;
        \defer($_, function () {
            PhpStubGenerator::$addClassConstantsVisibility = false;
        });

        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $class = $this->createReflectionClassMock();
        $const = $this->createReflectionConstMock();

        $class->method('getName')->willReturn('TestClass');
        $class->method('getReflectionConstant')->with('SUPER_CONSTANT')->willReturn($const);

        $const->method('getDocComment')->willReturn(false);
        $const->method('getName')->willReturn('SUPER_CONSTANT');
        $const->method('getValue')->willReturn(true);
        $const->method('getDeclaringClass')->willReturn($class);
        $const->method('isPublic')->willReturn(false);
        $const->method('isProtected')->willReturn(false);
        $const->method('isPrivate')->willReturn(true);

        /**
         * @var \ReflectionClassConstant $const
         */
        $expectedOutput = $t . $t . 'private const SUPER_CONSTANT = true;' . $n . $n;
        $this->assertSame($expectedOutput, (new ConstantFormatter('TestClass', $const))->format());
    }
}
