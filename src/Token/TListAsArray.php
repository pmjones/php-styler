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
        $openOffset = $parser->findNextNonWhitespaceOffset();

        if ($openOffset === null) {
            return;
        }

        // Find the matching ')'
        $closeOffset = $parser->findMatchingCloseParenOffset($openOffset);

        if ($closeOffset === null) {
            return;
        }

        // Replace '(' with '[' and ')' with ']'
        $open = $parser->getSourceAt($openOffset);
        $parser->setSourceAt(
            $openOffset,
            new PhpToken(ord('['), '[', $open->line, $open->pos),
        );

        $close = $parser->getSourceAt($closeOffset);
        $parser->setSourceAt(
            $closeOffset,
            new PhpToken(ord(']'), ']', $close->line, $close->pos),
        );

        // Don't emit any token for 'list' keyword
    }
}
