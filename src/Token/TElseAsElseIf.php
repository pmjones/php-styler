<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TElseAsElseIf extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if (! $parser->getNextSource()?->is(T_IF)) {
            TElse::parse($parser, $source);
            return;
        }

        // Find the T_IF offset in source, skipping T_WHITESPACE
        $ifOffset = $parser->getSourceOffset() + 1;

        while ($parser->getSourceAt($ifOffset)->is(T_WHITESPACE)) {
            $ifOffset ++;
        }

        // Replace T_IF with T_ELSEIF
        $if = $parser->getSourceAt($ifOffset);
        $parser->setSourceAt(
            $ifOffset,
            new PhpToken(T_ELSEIF, 'elseif', $if->line, $if->pos),
        );

        // Don't emit 'else' keyword; the main loop will pick up the T_ELSEIF
    }
}
