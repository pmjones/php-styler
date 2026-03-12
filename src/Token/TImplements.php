<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_IMPLEMENTS
 *
 * Syntax: implements
 *
 * Reference: https://www.php.net/manual/en/language.oop5.interfaces.php Object Interfaces
 */
class TImplements extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->add($source, static::class);
        $parser->space();
        $parser->addSplit(new TSplitListComma(T_WHITESPACE, ''));
    }
}
