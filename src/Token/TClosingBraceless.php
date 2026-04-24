<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Exception;
use PhpStyler\Parser;
use PhpToken;

class TClosingBraceless extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->source->peek()?->is([T_ELSE, T_ELSEIF])) {
            $parser->parse($source, TContinuationBraceless::class);
            return;
        }

        $parser->add($source, TSemicolon::class);
        $parser->popNesting(TOpeningBraceless::class);

        // The default arm enforces the invariant that TOpeningBraceless
        // only sits atop one of the six control nestings listed here; it
        // is unreachable via valid PHP source but is exercised by a
        // synthetic-stack test that manufactures a pathological nesting.
        $closingBraceless = match ($parser->getNesting()) {
            TIf::class => TIfClosingBraceless::class,
            TElse::class => TElseClosingBraceless::class,
            TElseif::class => TElseifClosingBraceless::class,
            TWhile::class => TWhileClosingBraceless::class,
            TFor::class => TForClosingBraceless::class,
            TForeach::class => TForeachClosingBraceless::class,

            default
                => throw Exception::fromParser(
                    "Unknown kind of closing braceless on line {$source->line}"
                        . " at position {$source->pos}",
                    $parser,
                    $source,
                ),
        };

        $parser->parse($source, $closingBraceless);
    }
}
