<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Exception;
use PhpStyler\Parser;
use PhpToken;

class TContinuationBraceless extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->add($source, TSemicolon::class);
        $parser->popNesting(TOpeningBraceless::class);

        // The default arm enforces the invariant that a braceless-body
        // continuation only arises under TIf or TElseif; it is unreachable
        // via valid PHP source but is exercised by a synthetic-stack test
        // that manufactures a pathological nesting.
        $partingBraceless = match ($parser->getNesting()) {
            TIf::class => TIfContinuationBraceless::class,
            TElseif::class => TElseifContinuationBraceless::class,

            default
                => throw Exception::fromParser(
                    "Unknown kind of parting braceless on line {$source->line}"
                        . " at position {$source->pos}",
                    $parser,
                    $source,
                ),
        };

        $parser->parse($source, $partingBraceless);
    }
}
