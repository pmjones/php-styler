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
class TInlineHtml extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->getPrevParsed() instanceof THaltCompilerSemicolon) {
            $source->text = rtrim($source->text, "\r\n");
        }

        $parser->add($source, self::class);
    }
}
