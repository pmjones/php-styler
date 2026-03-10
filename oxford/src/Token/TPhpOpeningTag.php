<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_OPEN_TAG
 *
 * Syntax: <?php, <? or <%
 *
 * Reference: https://www.php.net/manual/en/language.basic-syntax.phpmode.php escaping from HTML
 */
class TPhpOpeningTag extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $eol = strpos($unparsed->text, "\n") !== false
            || strpos($unparsed->text, "\r") !== false;

        $class = $eol ? self::class : TPhpOpeningTagInline::class;
        $trimmed = rtrim($unparsed->text);

        $openTagToken = new PhpToken(
            T_OPEN_TAG,
            $trimmed,
            $unparsed->line,
            $unparsed->pos,
        );

        $parser->add($openTagToken, $class);

        if ($eol) {
            $parser->lineBreak();
        }

        $whitespace = substr($unparsed->text, strlen($trimmed));

        if ($whitespace === '') {
            return;
        }

        $parser->parse(
            new PhpToken(
                T_WHITESPACE,
                $whitespace,
                $unparsed->line,
                $unparsed->pos + strlen($trimmed),
            ),
            TWhitespace::class,
        );
    }
}
