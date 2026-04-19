<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TElseAsElseIf extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if (! $parser->source->peek()?->is(T_IF)) {
            TElse::parse($parser, $source);
            return;
        }

        // Find the T_IF offset in source, skipping T_WHITESPACE
        $ifOffset = $parser->source->findNextNonWhitespace();

        if ($ifOffset === null) {
            TElse::parse($parser, $source);
            return;
        }

        // Replace T_IF with T_ELSEIF
        $if = $parser->source->getAt($ifOffset);

        $parser->source
            ->replaceAt(
                $ifOffset,
                new PhpToken(T_ELSEIF, 'elseif', $if->line, $if->pos),
            );

        // Don't emit 'else' keyword; the main loop will pick up the T_ELSEIF
    }
}
