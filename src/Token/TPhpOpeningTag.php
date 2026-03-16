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
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $eol = strpos($source->text, "\n") !== false
            || strpos($source->text, "\r") !== false;

        $class = $eol ? self::class : TPhpOpeningTagInline::class;
        $trimmed = rtrim($source->text);

        $openTagToken = new PhpToken(
            T_OPEN_TAG,
            $trimmed,
            $source->line,
            $source->pos,
        );

        $parser->add($openTagToken, $class);

        $whitespace = substr($source->text, strlen($trimmed));

        if ($whitespace === '') {
            return;
        }

        $parser->parse(
            new PhpToken(
                T_WHITESPACE,
                $whitespace,
                $source->line,
                $source->pos + strlen($trimmed),
            ),
            TWhitespace::class,
        );
    }
}
