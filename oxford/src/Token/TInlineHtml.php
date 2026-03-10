<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_INLINE_HTML
 *
 * Syntax: (n/a)
 *
 * Reference: https://www.php.net/manual/en/language.basic-syntax.phpmode.php text outside PHP
 */
class TInlineHtml extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if ($parser->getPrevParsed() instanceof THaltCompilerSemicolon) {
            $unparsed->text = rtrim($unparsed->text, "\r\n");
        }

        $parser->add($unparsed, self::class);
    }
}
