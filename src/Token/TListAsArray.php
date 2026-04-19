<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TListAsArray extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        // Find the '(' after 'list', skipping whitespace
        $openOffset = $parser->source->findNextNonWhitespace();

        if ($openOffset === null) {
            return;
        }

        // Find the matching ')'
        $closeOffset = $parser->source->matchingCloseParen($openOffset);

        if ($closeOffset === null) {
            return;
        }

        // Replace '(' with '[' and ')' with ']'
        $open = $parser->source->getAt($openOffset);

        $parser->source
            ->replaceAt(
                $openOffset,
                new PhpToken(ord('['), '[', $open->line, $open->pos),
            );

        $close = $parser->source->getAt($closeOffset);

        $parser->source
            ->replaceAt(
                $closeOffset,
                new PhpToken(ord(']'), ']', $close->line, $close->pos),
            );

        // Don't emit any token for 'list' keyword
    }
}
