<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_OPEN_TAG_WITH_ECHO
 *
 * Syntax: <?= or <%=
 *
 * Reference: https://www.php.net/manual/en/language.basic-syntax.phpmode.php escaping from HTML
 */
class TPhpEchoOpeningTag extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $class = $parser->getPrevParsed() instanceof TInlineHtml
            ? TPhpEchoOpeningTagContinuation::class
            : static::class;

        $parser->add($source, $class);
    }
}
