<?php

declare(strict_types=1);

namespace setasign\PhpStubGenerator\Tests\unit\Formatter;

use PHPUnit\Framework\TestCase;
use ReflectionFunction;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use setasign\PhpStubGenerator\Formatter\FunctionFormatter;
use setasign\PhpStubGenerator\PhpStubGenerator;

class FunctionFormatterTest extends TestCase
{
    protected function createReflectionTypeMock(string $name, bool $allowsNull = false): ReflectionNamedType
    {
        $result = $this->getMockBuilder(ReflectionNamedType::class)
            ->onlyMethods(['allowsNull', '__toString'])
            ->disableOriginalConstructor()
            ->getMock();

        $result->method('allowsNull')->willReturn($allowsNull);
        $result->method('__toString')->willReturn($name);

        return $result;
    }

    protected function createReflectionParameterMock(
        string $name,
        ?ReflectionType $type,
        bool $hasDefault,
        string $defaultConstant,
        $defaultValue,
        bool $isVariadic,
        bool $isPassedByReference
    ): ReflectionParameter {
        $result = $this->getMockBuilder(ReflectionParameter::class)
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
            $result->method('isDefaultValueConstant')->willReturn($defaultConstant !== '');
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

        return $result;
    }

    protected function createReflectionFunctionMock(
        string $name,
        array $parameters,
        ?ReflectionType $returnType,
        ?string $doc
    ): ReflectionFunction {
        $result = $this->getMockBuilder(ReflectionFunction::class)
            ->onlyMethods([
                'getName',
                'getParameters',
                'hasReturnType',
                'getReturnType',
                'getDocComment'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $result->method('getName')->willReturn($name);
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

        return $result;
    }

    /**
     * @throws \Throwable
     */
    public function testSimpleFunction(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $reflectionFunction = $this->createReflectionFunctionMock('test', [], null, null);

        $expectedOutput = $t . 'function test() {}' . $n . $n;
        $this->assertSame($expectedOutput, (new FunctionFormatter($reflectionFunction))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testFunctionWithReturnInt(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $returnType = $this->createReflectionTypeMock('int');
        $reflectionFunction = $this->createReflectionFunctionMock('test', [], $returnType, null);

        $expectedOutput = $t . 'function test(): int {}' . $n . $n;
        $this->assertSame($expectedOutput, (new FunctionFormatter($reflectionFunction))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testFunctionWithReturnObject(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $returnType = $this->createReflectionTypeMock('stdClass');
        $reflectionFunction = $this->createReflectionFunctionMock('test', [], $returnType, null);

        $expectedOutput = $t . 'function test(): \stdClass {}' . $n . $n;
        $this->assertSame($expectedOutput, (new FunctionFormatter($reflectionFunction))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testFunctionWithParams(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $a = $this->createReflectionParameterMock(
            'a',
            $this->createReflectionTypeMock('stdClass'),
            false,
            '',
            null,
            false,
            false
        );

        $b = $this->createReflectionParameterMock(
            'b',
            $this->createReflectionTypeMock('int', true),
            false,
            '',
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
            '',
            123,
            false,
            false
        );
        $reflectionFunction = $this->createReflectionFunctionMock('test', [$a, $b, $c, $d], null, null);

        $expectedOutput = $t . 'function test(\stdClass $a, ?int $b, $c = TEST_CONSTANT, $d = 123) {}' . $n . $n;
        $this->assertSame($expectedOutput, (new FunctionFormatter($reflectionFunction))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testFunctionWithVariadicParam(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $a = $this->createReflectionParameterMock(
            'a',
            $this->createReflectionTypeMock('int'),
            false,
            '',
            null,
            false,
            false
        );

        $b = $this->createReflectionParameterMock(
            'b',
            null,
            false,
            '',
            null,
            true,
            false
        );
        $reflectionFunction = $this->createReflectionFunctionMock('test', [$a, $b], null, null);

        $expectedOutput = $t . 'function test(int $a, ...$b) {}' . $n . $n;
        $this->assertSame($expectedOutput, (new FunctionFormatter($reflectionFunction))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testFunctionWithPassedByReferenceParam(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $a = $this->createReflectionParameterMock(
            'a',
            $this->createReflectionTypeMock('int'),
            false,
            '',
            null,
            false,
            true
        );

        $b = $this->createReflectionParameterMock(
            'b',
            null,
            false,
            '',
            null,
            false,
            true
        );
        $reflectionFunction = $this->createReflectionFunctionMock('test', [$a, $b], null, null);

        $expectedOutput = $t . 'function test(int &$a, &$b) {}' . $n . $n;
        $this->assertSame($expectedOutput, (new FunctionFormatter($reflectionFunction))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testFunctionWithVariadicPassedByReferenceParam(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $a = $this->createReflectionParameterMock(
            'a',
            $this->createReflectionTypeMock('int'),
            false,
            '',
            null,
            false,
            false
        );

        $b = $this->createReflectionParameterMock(
            'b',
            null,
            false,
            '',
            null,
            true,
            true
        );
        $reflectionFunction = $this->createReflectionFunctionMock('test', [$a, $b], null, null);

        $expectedOutput = $t . 'function test(int $a, &...$b) {}' . $n . $n;
        $this->assertSame($expectedOutput, (new FunctionFormatter($reflectionFunction))->format());
    }

    /**
     * @throws \Throwable
     */
    public function testFunctionWithDoc(): void
    {
        $n = PhpStubGenerator::$eol;
        $t = PhpStubGenerator::$tab;

        $reflectionFunction = $this->createReflectionFunctionMock('test', [], null, <<<EOT
/**
 * This is a cool test document!
 *
 * Just for test.
 */
EOT
        );

        $expectedOutput = ''
            . $t. '/**' . $n
            . $t. ' * This is a cool test document!' . $n
            . $t. ' *' . $n
            . $t. ' * Just for test.' . $n
            . $t. ' */' . $n
            . $t . 'function test() {}' . $n . $n;
        $this->assertSame($expectedOutput, (new FunctionFormatter($reflectionFunction))->format());
    }
}
