<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Tests\unit\Formatter;

use ReflectionMethod;
use PHPUnit\Framework\TestCase;
use setasign\PhpStubGenerator\Formatter\MethodFormatter;
use setasign\PhpStubGenerator\PhpStubGenerator;

class MethodFormatterTest extends TestCase
{
    protected function createReflectionTypeMock(string $name, bool $allowsNull = false): \ReflectionType
    {
        $result = $this->getMockBuilder(\ReflectionType::class)
            ->onlyMethods(['allowsNull', '__toString'])
            ->disableOriginalConstructor()
            ->getMock();

        $result->method('allowsNull')->willReturn($allowsNull);
        $result->method('__toString')->willReturn($name);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $result;
    }

    protected function createReflectionParameterMock(
        string $name,
        ?\ReflectionType $type,
        bool $hasDefault,
        ?string $defaultConstant,
        $defaultValue,
        bool $isVariadic,
        bool $isPassedByReference
    ): \ReflectionParameter {
        $result = $this->getMockBuilder(\ReflectionParameter::class)
            ->onlyMethods([
                'getName',
                'getType',
                'isDefaultValueAvailable',
                'isDefaultValueConstant',
                'getDefaultValueConstantName',
                'getDefaultValue',
                'isVariadic',
                'isPassedByReference'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $result->method('getName')->willReturn($name);
        $result->method('getType')->willReturn($type);
        $result->method('isDefaultValueAvailable')->willReturn($hasDefault);
        if ($hasDefault) {
            $result->method('isDefaultValueConstant')->willReturn($defaultConstant !== null);
            $result->method('getDefaultValueConstantName')->willReturn($defaultConstant);
            $result->method('getDefaultValue')->willReturn($defaultValue);
        } else {
            $result->method('isDefaultValueConstant')->willThrowException(
                new \Exception('Can\'t resolve default value')
            );
            $result->method('getDefaultValueConstantName')->willThrowException(
                new \Exception('Can\'t resolve default value')
            );
            $result->method('getDefaultValue')->willThrowException(
                new \Exception('Can\'t resolve default value')
            );
        }
        $result->method('isVariadic')->willReturn($isVariadic);
        $result->method('isPassedByReference')->willReturn($isPassedByReference);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $result;
    }

    protected function createReflectionMethodMock(
        string $name,
        string $declaringClassName,
        int $modifiers,
        array $parameters,
        ?\ReflectionType $returnType,
        ?string $doc
    ): ReflectionMethod {
        $result = $this->getMockBuilder(ReflectionMethod::class)
            ->onlyMethods([
                'getName',
                'getDeclaringClass',
                'getParameters',
                'hasReturnType',
                'getReturnType',
                'getDocComment',
                'isAbstract',
                'isFinal',
                'isPublic',
                'isProtected',
                'isPrivate',
                'isStatic'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $result->method('getName')->willReturn($name);

        $declaringClass = $this->getMockBuilder(\ReflectionClass::class)
            ->onlyMethods(['getName'])
            ->disableOriginalConstructor()
            ->getMock();

        $declaringClass->method('getName')->willReturn($declaringClassName);

        $result->method('getDeclaringClass')->willReturn($declaringClass);

        $checkFlag = function ($flag) use ($modifiers): bool {
            return ($modifiers & $flag) === $flag;
        };
        $result->method('isAbstract')->willReturn($checkFlag(ReflectionMethod::IS_ABSTRACT));
        $result->method('isFinal')->willReturn($checkFlag(ReflectionMethod::IS_FINAL));
        $result->method('isPublic')->willReturn($checkFlag(ReflectionMethod::IS_PUBLIC));
        $result->method('isProtected')->willReturn($checkFlag(ReflectionMethod::IS_PROTECTED));
        $result->method('isPrivate')->willReturn($checkFlag(ReflectionMethod::IS_PRIVATE));
        $result->method('isStatic')->willReturn($checkFlag(ReflectionMethod::IS_STATIC));

        $result->method('getParameters')->willReturn($parameters);
        $hasReturnType = $returnType !== null;
        $result->method('hasReturnType')->willReturn($hasReturnType);
        if ($hasReturnType) {
            $result->method('getReturnType')->willReturn($returnType);
        } else {
            $result->method('getReturnType')->willThrowException(new \Exception('Can\'t resolve return type'));
        }

        /** @noinspection ProperNullCoalescingOperatorUsageInspection */
        $result->method('getDocComment')->willReturn($doc ?? false);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $result;
    }

    /**
     * @throws \Throwable
     */
    public function testPublicMethod(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $modifiers = ReflectionMethod::IS_PUBLIC;
        $method = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [], null, null);

        $expectedOutput = $t . $t . 'public function test() {}' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $method))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testProtectedMethod(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $modifiers = ReflectionMethod::IS_PROTECTED;
        $method = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [], null, null);

        $expectedOutput = $t . $t . 'protected function test() {}' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $method))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testPrivateMethod(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $modifiers = ReflectionMethod::IS_PRIVATE;
        $method = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [], null, null);

        $expectedOutput = $t . $t . 'private function test() {}' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $method))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testStaticMethod(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $modifiers = ReflectionMethod::IS_STATIC | ReflectionMethod::IS_PUBLIC;
        $method = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [], null, null);

        $expectedOutput = $t . $t . 'public static function test() {}' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $method))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testFinalMethod(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $modifiers = ReflectionMethod::IS_FINAL | ReflectionMethod::IS_PUBLIC;
        $method = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [], null, null);

        $expectedOutput = $t . $t . 'final public function test() {}' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $method))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testAbstractMethod(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $modifiers = ReflectionMethod::IS_ABSTRACT | ReflectionMethod::IS_PUBLIC;
        $method = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [], null, null);

        $expectedOutput = $t . $t . 'abstract public function test();' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $method))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testMethodFromParent(): void
    {
        $modifiers = ReflectionMethod::IS_PUBLIC;
        $method = $this->createReflectionMethodMock('test', 'AnotherClass', $modifiers, [], null, null);

        $expectedOutput = '';
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $method))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testMethodInInterface(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $modifiers = ReflectionMethod::IS_PUBLIC;
        $method = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [], null, null);

        $expectedOutput = $t . $t . 'public function test();' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', true, $method))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testMethodWithReturnInt(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $returnType = $this->createReflectionTypeMock('int');
        $modifiers = ReflectionMethod::IS_PUBLIC;
        $reflectionMethod = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [], $returnType, null);

        $expectedOutput = $t . $t . 'public function test(): int {}' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $reflectionMethod))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testMethodWithReturnObject(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $returnType = $this->createReflectionTypeMock('stdClass');
        $modifiers = ReflectionMethod::IS_PUBLIC;
        $reflectionMethod = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [], $returnType, null);

        $expectedOutput = $t . $t . 'public function test(): \stdClass {}' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $reflectionMethod))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testMethodWithParams(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $a = $this->createReflectionParameterMock(
            'a',
            $this->createReflectionTypeMock('stdClass'),
            false,
            null,
            null,
            false,
            false
        );

        $b = $this->createReflectionParameterMock(
            'b',
            $this->createReflectionTypeMock('int', true),
            false,
            null,
            null,
            false,
            false
        );

        $c = $this->createReflectionParameterMock(
            'c',
            null,
            true,
            'TEST_CONSTANT',
            null,
            false,
            false
        );

        $d = $this->createReflectionParameterMock(
            'd',
            null,
            true,
            null,
            123,
            false,
            false
        );
        $modifiers = ReflectionMethod::IS_PUBLIC;
        $reflectionMethod = $this->createReflectionMethodMock(
            'test',
            'SomeClass',
            $modifiers,
            [$a, $b, $c, $d],
            null,
            null
        );

        $expectedOutput = $t . $t . 'public function test(\stdClass $a, ?int $b, $c = TEST_CONSTANT, $d = 123) {}' . $n
            . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $reflectionMethod))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testMethodWithVariadicParam(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $a = $this->createReflectionParameterMock(
            'a',
            $this->createReflectionTypeMock('int'),
            false,
            null,
            null,
            false,
            false
        );

        $b = $this->createReflectionParameterMock(
            'b',
            null,
            false,
            null,
            null,
            true,
            false
        );
        $modifiers = ReflectionMethod::IS_PUBLIC;
        $reflectionMethod = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [$a, $b], null, null);

        $expectedOutput = $t . $t . 'public function test(int $a, ...$b) {}' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $reflectionMethod))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testMethodWithPassedByReferenceParam(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $a = $this->createReflectionParameterMock(
            'a',
            $this->createReflectionTypeMock('int'),
            false,
            null,
            null,
            false,
            true
        );

        $b = $this->createReflectionParameterMock(
            'b',
            null,
            false,
            null,
            null,
            false,
            true
        );
        $modifiers = ReflectionMethod::IS_PUBLIC;
        $reflectionMethod = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [$a, $b], null, null);

        $expectedOutput = $t . $t . 'public function test(int &$a, &$b) {}' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $reflectionMethod))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testMethodWithVariadicPassedByReferenceParam(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $a = $this->createReflectionParameterMock(
            'a',
            $this->createReflectionTypeMock('int'),
            false,
            null,
            null,
            false,
            false
        );

        $b = $this->createReflectionParameterMock(
            'b',
            null,
            false,
            null,
            null,
            true,
            true
        );
        $modifiers = ReflectionMethod::IS_PUBLIC;
        $reflectionMethod = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [$a, $b], null, null);

        $expectedOutput = $t . $t . 'public function test(int $a, &...$b) {}' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $reflectionMethod))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testMethodWithDoc(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $modifiers = ReflectionMethod::IS_PUBLIC;
        $reflectionMethod = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [], null, <<<EOT
/**
 * This is a cool test document!
 *
 * Just for test.
 */
EOT
        );

        $expectedOutput = ''
            . $t . $t . '/**' . $n
            . $t . $t . ' * This is a cool test document!' . $n
            . $t . $t . ' *' . $n
            . $t . $t . ' * Just for test.' . $n
            . $t . $t . ' */' . $n
            . $t . $t . 'public function test() {}' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $reflectionMethod))->format());
    }
}
