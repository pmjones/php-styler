<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_WHITESPACE
 *
 * Syntax: \t \r\n
 *
 * Reference: (n/a)
 */
class TWhitespace extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        /** @var list<string> $segments */
        $segments = preg_split(
            pattern: '/(\r\n|\n|\r|[ \t]+)/',
            subject: $unparsed->text,
            flags: PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        $line = $unparsed->line;
        $pos = $unparsed->pos;

        foreach ($segments as $i => $segment) {
            $segmentToken = new PhpToken(
                T_WHITESPACE,
                $segment,
                $line,
                $pos,
            );

            $char = substr($segment, 0, 1);

            if ($char === "\r" || $char === "\n") {
                $parser->parse($segmentToken, TWhitespaceEol::class);
                $line += substr_count($segment, "\n");
            }

            $pos += strlen($segment);
        }

    }
}
