<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

/**
 * Token: T_ARRAY
 *
 * Syntax: array (as typehint)
 *
 * Reference: https://www.php.net/manual/en/language.types.array.php
 */
class TArray extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if ($parser->getNextUnparsed()?->is('(')) {
            $parser->addNesting($unparsed, TArrayConstruct::class);
        } else {
            $parser->add($unparsed, self::class);
        }
    }
}
