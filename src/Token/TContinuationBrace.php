<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Exception;
use PhpStyler\Parser;
use PhpToken;

class TContinuationBrace extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        // The default arm enforces the invariant that a `}` continuation
        // only arises under TIf / TElseif / TTry / TCatch / TDo nestings;
        // it is unreachable via valid PHP source but is exercised by a
        // synthetic-stack test that manufactures a pathological nesting.
        $partingBrace = match ($parser->getNesting()) {
            TIf::class => TIfContinuationBrace::class,
            TElseif::class => TElseifContinuationBrace::class,
            TTry::class => TTryContinuationBrace::class,
            TCatch::class => TCatchContinuationBrace::class,
            TDo::class => TDoContinuationBrace::class,

            default
                => throw Exception::fromParser(
                    "Unknown kind of parting brace on line {$source->line}"
                        . " at position {$source->pos}",
                    $parser,
                    $source,
                ),
        };

        $parser->parse($source, $partingBrace);
    }
}
