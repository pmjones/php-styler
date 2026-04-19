<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_ARRAY
 *
 * Syntax: array (as typehint)
 *
 * Reference: https://www.php.net/manual/en/language.types.array.php
 */
class TArray extends AToken implements AType
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->source->peek()?->is('(')) {
            $parser->addNesting($source, TArrayConstruct::class);
        } else {
            $parser->add($source, self::class);
        }
    }
}
