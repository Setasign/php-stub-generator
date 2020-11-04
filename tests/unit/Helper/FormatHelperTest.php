<?php

declare(strict_types=1);

namespace setasign\PhpStubGenerator\Tests\unit\Helper;

use PHPUnit\Framework\TestCase;
use setasign\PhpStubGenerator\Helper\FormatHelper;

class FormatHelperTest extends TestCase
{
    /**
     * @return array
     */
    public function indentDocBlockDataProvider(): array
    {
        $t1 = '    ';
        $t2 = "\t";

        return [
            ['asdf', 'asdf', 0, $t1],
            ['asdf', 'asdf', 0, $t2],
            [$t1 . 'asdf', 'asdf', 1, $t1],
            [$t2 . 'asdf', 'asdf', 1, $t2],
            ["$t1/**\n$t1 * a\n$t1 * b\n$t1 */", "/**\n * a\n* b\n */", 1, $t1],
            ["$t1/**\n$t1 * a\n$t1 * b\n$t1 */", "/**\r\n * a\r\n* b\r\n */", 1, $t1],
            [
                "$t2$t2$t2/**\n"
                . "$t2$t2$t2 * a\n"
                . "$t2$t2$t2 * b\n"
                . "$t2$t2$t2 * c\n"
                . "$t2$t2$t2 * d\n"
                . "$t2$t2$t2 * e\n"
                . "$t2$t2$t2 * f\n"
                . "$t2$t2$t2 */",
                "/**\n"
                . "* a\n"
                . "* b\n"
                . "* c\n"
                . "* d\n"
                . "* e\n"
                . "* f\n"
                . '*/',
                3,
                $t2
            ]
        ];
    }

    /**
     * @dataProvider indentDocBlockDataProvider
     *
     * @param string $expectedOutput
     * @param string $block
     * @param int $tabCount
     * @param string $tabChar
     */
    public function testIndentDocBlock(string $expectedOutput, string $block, int $tabCount, string $tabChar): void
    {
        $this->assertSame(
            $expectedOutput,
            FormatHelper::indentDocBlock($block, $tabCount, $tabChar),
            \sprintf(
                'Block = "%s" TabCount = "%d" TabChar "%s"',
                $block,
                $tabCount,
                $tabChar
            )
        );
    }

    /**
     * @return array
     */
    public function formatValueDataProvider(): array
    {
        return [
            ['\'\'', ''],
            ['true', true],
            ['false', false],
            ['null', null],
            ['123', 123],
            ['123.123', 123.123],
            ['0.123', .123],
            ['1000000', 10e5],
            ['[/** value is missing */]', [1, 2, 3, 4]],
            ["'\x01\x02\x03'", "\x01\x02\x03"],
            ['"\xCA\x99\xFF\x61\x62\x63"', "\xCA\x99\xFFabc"],
            ['"\xCA\x99\xFF\x61\x62\x63\xA\x31\x32\x33"', "\xCA\x99\xFFabc\n123"]
        ];
    }

    /**
     * @dataProvider formatValueDataProvider
     *
     * @param string $expectedOutput
     * @param string $value
     */
    public function testFormatValue(string $expectedOutput, $value): void
    {
        $this->assertSame(
            $expectedOutput,
            FormatHelper::formatValue($value),
            \sprintf('Value = "%s"', \print_r($value, true))
        );
    }
}
