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
            ->setMethods(['allowsNull', '__toString'])
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
        bool $isVariadic
    ): \ReflectionParameter {
        $result = $this->getMockBuilder(\ReflectionParameter::class)
            ->setMethods([
                'getName',
                'getType',
                'isDefaultValueAvailable',
                'isDefaultValueConstant',
                'getDefaultValueConstantName',
                'getDefaultValue',
                'isVariadic'
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
            ->setMethods([
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
            ->setMethods(['getName'])
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

        $result->method('getDocComment')->willReturn($doc ?? false);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $result;
    }

    public function testPublicMethod()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $modifiers = ReflectionMethod::IS_PUBLIC;
        $method = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [], null, null);

        $expectedOutput = $t . $t . 'public function test() {}' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $method))->format());
    }

    public function testProtectedMethod()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $modifiers = ReflectionMethod::IS_PROTECTED;
        $method = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [], null, null);

        $expectedOutput = $t . $t . 'protected function test() {}' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $method))->format());
    }

    public function testPrivateMethod()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $modifiers = ReflectionMethod::IS_PRIVATE;
        $method = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [], null, null);

        $expectedOutput = $t . $t . 'private function test() {}' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $method))->format());
    }

    public function testStaticMethod()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $modifiers = ReflectionMethod::IS_STATIC | ReflectionMethod::IS_PUBLIC;
        $method = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [], null, null);

        $expectedOutput = $t . $t . 'public static function test() {}' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $method))->format());
    }

    public function testFinalMethod()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $modifiers = ReflectionMethod::IS_FINAL | ReflectionMethod::IS_PUBLIC;
        $method = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [], null, null);

        $expectedOutput = $t . $t . 'final public function test() {}' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $method))->format());
    }

    public function testAbstractMethod()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $modifiers = ReflectionMethod::IS_ABSTRACT | ReflectionMethod::IS_PUBLIC;
        $method = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [], null, null);

        $expectedOutput = $t . $t . 'abstract public function test();' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $method))->format());
    }

    public function testMethodFromParent()
    {
        $modifiers = ReflectionMethod::IS_PUBLIC;
        $method = $this->createReflectionMethodMock('test', 'AnotherClass', $modifiers, [], null, null);

        $expectedOutput = '';
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $method))->format());
    }

    public function testMethodInInterface()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $modifiers = ReflectionMethod::IS_PUBLIC;
        $method = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [], null, null);

        $expectedOutput = $t . $t . 'public function test();' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', true, $method))->format());
    }

    public function testMethodWithReturnInt()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $returnType = $this->createReflectionTypeMock('int');
        $modifiers = ReflectionMethod::IS_PUBLIC;
        $reflectionMethod = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [], $returnType, null);

        $expectedOutput = $t . $t . 'public function test(): int {}' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $reflectionMethod))->format());
    }

    public function testMethodWithReturnObject()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $returnType = $this->createReflectionTypeMock('stdClass');
        $modifiers = ReflectionMethod::IS_PUBLIC;
        $reflectionMethod = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [], $returnType, null);

        $expectedOutput = $t . $t . 'public function test(): \stdClass {}' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $reflectionMethod))->format());
    }

    public function testMethodWithParams()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $a = $this->createReflectionParameterMock(
            'a',
            $this->createReflectionTypeMock('stdClass'),
            false,
            null,
            null,
            false
        );

        $b = $this->createReflectionParameterMock(
            'b',
            $this->createReflectionTypeMock('int', true),
            false,
            null,
            null,
            false
        );

        $c = $this->createReflectionParameterMock(
            'c',
            null,
            true,
            'TEST_CONSTANT',
            null,
            false
        );

        $d = $this->createReflectionParameterMock(
            'd',
            null,
            true,
            null,
            123,
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

    public function testMethodWithVariadicParam()
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $a = $this->createReflectionParameterMock(
            'a',
            $this->createReflectionTypeMock('int'),
            false,
            null,
            null,
            false
        );

        $b = $this->createReflectionParameterMock(
            'b',
            null,
            false,
            null,
            null,
            true
        );
        $modifiers = ReflectionMethod::IS_PUBLIC;
        $reflectionMethod = $this->createReflectionMethodMock('test', 'SomeClass', $modifiers, [$a, $b], null, null);

        $expectedOutput = $t . $t . 'public function test(int $a, ...$b) {}' . $n . $n;
        $this->assertSame($expectedOutput, (new MethodFormatter('SomeClass', false, $reflectionMethod))->format());
    }

    public function testMethodWithDoc()
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
