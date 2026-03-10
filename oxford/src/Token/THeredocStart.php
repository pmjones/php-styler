<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_START_HEREDOC
 *
 * Syntax: <<<
 *
 * Reference: https://www.php.net/manual/en/language.types.string.php#language.types.string.syntax.heredoc heredoc syntax
 */
class THeredocStart extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->addNesting($unparsed, self::class);
    }
}
