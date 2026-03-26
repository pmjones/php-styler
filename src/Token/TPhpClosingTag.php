<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_CLOSE_TAG
 *
 * Syntax: ?> or %>
 *
 * Reference: https://www.php.net/manual/en/language.basic-syntax.phpmode.php escaping from HTML
 */
class TPhpClosingTag extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->add($source, static::class);
    }
}
