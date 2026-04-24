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

        $nesting = $parser->getNesting();

        $closingBraceless = match ($nesting) {
            TIf::class => TIfClosingBraceless::class,
            TElse::class => TElseClosingBraceless::class,
            TElseif::class => TElseifClosingBraceless::class,
            TWhile::class => TWhileClosingBraceless::class,
            TFor::class => TForClosingBraceless::class,
            TForeach::class => TForeachClosingBraceless::class,
            default => null,
        };

        if ($closingBraceless) {
            $parser->parse($source, $closingBraceless);
            return;
        }

        // @codeCoverageIgnoreStart
        // defensive: a braceless body only closes under one of the nesting
        // types handled above, so the default arm is unreachable for accepted
        // input
        $message = "Unknown kind of closing braceless"
            . " on line {$source->line}"
            . " at position {$source->pos}";

        throw Exception::fromParser($message, $parser, $source);
        // @codeCoverageIgnoreEnd
    }
}
