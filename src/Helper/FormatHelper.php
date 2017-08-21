<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Helper;

class FormatHelper
{
    /**
     * @param string $block
     * @param int $tabCount
     * @param string $tabChar
     * @return string
     */
    public static function indentDocBlock(string $block, int $tabCount, string $tabChar): string
    {
        $tab = str_repeat($tabChar, $tabCount);
        
        return $tab . preg_replace("/\r?\n( |\t)*/", "\n" . $tab .  ' ', $block);
    }

    public static function formatValue($value): string
    {
        if ($value === null) {
            $value = 'null';
        } elseif (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        } elseif (is_string($value)) {
            if (!mb_check_encoding($value, 'UTF-8')) {
                $t = str_split($value);
                $t = array_map(function ($char) {
                    return '\\x' . strtoupper(dechex(ord($char)));
                }, $t);
                $value = '"' . implode($t) . '"';
            } else {
                $value = '\'' . $value . '\'';
            }
        } elseif (is_array($value)) {
            $value = '[/** value is missing */]';
        }

        return '' . $value;
    }
}
