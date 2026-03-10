<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

/**
 * Token: T_END_HEREDOC
 *
 * Syntax: (n/a)
 *
 * Reference: https://www.php.net/manual/en/language.types.string.php#language.types.string.syntax.heredoc heredoc syntax
 */
class THeredocEnd extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->popNesting(THeredocStart::class);
        $parser->add($unparsed, self::class);
        $parser->space();
    }
}
