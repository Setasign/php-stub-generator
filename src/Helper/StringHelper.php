<?php
declare(strict_types=1);

namespace setasign\PhpStubGenerator\Helper;

class StringHelper
{
    /**
     * @param string $block
     * @param int $tabCount
     * @param string $tabChar
     * @return string
     */
    public static function indentBlock(string $block, int $tabCount, string $tabChar): string
    {
        $tab = str_repeat($tabChar, $tabCount);
        
        return $tab . preg_replace("/\n( |\t)*/", "\n " . $tab, $block);
    }
}
