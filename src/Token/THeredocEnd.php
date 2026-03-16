<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
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
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popNesting(THeredocStart::class);
        $parser->add($source, self::class);
    }
}
